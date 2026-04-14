/**
 * Mock Location Detector for Chrome Mobile
 * Detects fake/mock GPS locations using multiple heuristic checks.
 * 
 * Techniques:
 * 1. Accuracy anomaly detection (accuracy = 0 or < 1m is suspicious)
 * 2. GPS teleportation detection (position jumps > 100m in < 2s)
 * 3. Timestamp consistency check
 * 4. Multi-read validation (take multiple samples, compare)
 * 5. Device sensor cross-check (motion sensors vs GPS)
 */
class MockLocationDetector {
    constructor(options = {}) {
        this.options = {
            sampleCount: 3,
            sampleInterval: 1000,       // ms between samples
            maxAcceptableAccuracy: 500,  // meters — max accuracy we trust
            minSuspiciousAccuracy: 1,    // meters — too perfect = suspicious
            teleportThreshold: 100,      // meters — max realistic jump
            teleportTimeWindow: 2000,    // ms — time window for teleport check
            timeout: 15000,              // ms — GPS timeout
            ...options
        };

        this.samples = [];
        this.sensorData = { hasMotion: false, hasOrientation: false, motionDetected: false };
        this.reasons = [];
        this.onStatusUpdate = null;
    }

    /**
     * Main detection method — returns a comprehensive result
     * @returns {Promise<{isMockSuspected: boolean, confidence: number, reasons: string[], location: object}>}
     */
    async detect() {
        this.reasons = [];
        this.samples = [];

        // Step 1: Check if geolocation is available
        if (!navigator.geolocation) {
            throw new Error('Geolocation tidak didukung oleh browser ini.');
        }

        // Step 2: Start sensor monitoring
        this._startSensorMonitoring();
        this._updateStatus('Memeriksa sensor perangkat...');

        // Step 3: Collect multiple GPS samples
        this._updateStatus('Mengambil data lokasi GPS...');
        await this._collectSamples();

        // Step 4: Stop sensor monitoring
        this._stopSensorMonitoring();

        // Step 5: Analyze all data
        this._updateStatus('Menganalisis data lokasi...');
        const analysis = this._analyze();

        return analysis;
    }

    /**
     * Quick single-read detection (faster but less accurate)
     */
    async detectQuick() {
        this.reasons = [];
        this.samples = [];

        if (!navigator.geolocation) {
            throw new Error('Geolocation tidak didukung oleh browser ini.');
        }

        this._updateStatus('Mengambil lokasi GPS...');
        const position = await this._getPosition();
        this.samples.push(position);

        return this._analyze();
    }

    /**
     * Request location permission explicitly
     * @returns {Promise<string>} 'granted', 'denied', or 'prompt'
     */
    async requestPermission() {
        try {
            const result = await navigator.permissions.query({ name: 'geolocation' });
            return result.state;
        } catch (e) {
            // Fallback: try to get position to trigger permission dialog
            try {
                await this._getPosition();
                return 'granted';
            } catch (err) {
                if (err.code === 1) return 'denied';
                return 'prompt';
            }
        }
    }

    // ========================
    // Private methods
    // ========================

    _getPosition() {
        return new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    resolve({
                        latitude: pos.coords.latitude,
                        longitude: pos.coords.longitude,
                        accuracy: pos.coords.accuracy,
                        altitude: pos.coords.altitude,
                        altitudeAccuracy: pos.coords.altitudeAccuracy,
                        heading: pos.coords.heading,
                        speed: pos.coords.speed,
                        timestamp: pos.timestamp
                    });
                },
                (err) => {
                    let message = 'Gagal mendapatkan lokasi.';
                    switch (err.code) {
                        case 1: message = 'Izin lokasi ditolak. Silakan izinkan akses lokasi di pengaturan browser.'; break;
                        case 2: message = 'Lokasi tidak tersedia. Pastikan GPS aktif.'; break;
                        case 3: message = 'Waktu habis saat mengambil lokasi. Coba lagi.'; break;
                    }
                    reject(new Error(message));
                },
                {
                    enableHighAccuracy: true,
                    timeout: this.options.timeout,
                    maximumAge: 0
                }
            );
        });
    }

    async _collectSamples() {
        for (let i = 0; i < this.options.sampleCount; i++) {
            try {
                this._updateStatus(`Mengambil sampel GPS ${i + 1}/${this.options.sampleCount}...`);
                const position = await this._getPosition();
                this.samples.push(position);

                if (i < this.options.sampleCount - 1) {
                    await this._sleep(this.options.sampleInterval);
                }
            } catch (error) {
                // If we have at least 1 sample, continue with analysis
                if (this.samples.length > 0) break;
                throw error;
            }
        }
    }

    _analyze() {
        if (this.samples.length === 0) {
            throw new Error('Tidak ada data lokasi yang berhasil diambil.');
        }

        const lastSample = this.samples[this.samples.length - 1];
        let suspicionScore = 0;

        // Check 1: Accuracy anomaly
        const accuracyResult = this._checkAccuracy();
        suspicionScore += accuracyResult.score;

        // Check 2: Teleportation detection (if multiple samples)
        if (this.samples.length > 1) {
            const teleportResult = this._checkTeleportation();
            suspicionScore += teleportResult.score;
        }

        // Check 3: Timestamp consistency
        if (this.samples.length > 1) {
            const timestampResult = this._checkTimestamps();
            suspicionScore += timestampResult.score;
        }

        // Check 4: Multi-sample consistency
        if (this.samples.length > 1) {
            const consistencyResult = this._checkConsistency();
            suspicionScore += consistencyResult.score;
        }

        // Check 5: Sensor cross-check
        const sensorResult = this._checkSensors();
        suspicionScore += sensorResult.score;

        // Check 6: Missing data indicators
        const missingDataResult = this._checkMissingData();
        suspicionScore += missingDataResult.score;

        // Calculate confidence (0-1)
        const maxPossibleScore = 6; // max checks
        const confidence = Math.min(suspicionScore / maxPossibleScore, 1);
        const isMockSuspected = suspicionScore >= 2; // threshold: 2+ checks fail

        return {
            isMockSuspected,
            confidence: Math.round(confidence * 100) / 100,
            reasons: [...this.reasons],
            location: {
                latitude: lastSample.latitude,
                longitude: lastSample.longitude,
                accuracy: lastSample.accuracy
            },
            samplesCollected: this.samples.length,
            suspicionScore
        };
    }

    _checkAccuracy() {
        const accuracies = this.samples.map(s => s.accuracy);
        const avgAccuracy = accuracies.reduce((a, b) => a + b, 0) / accuracies.length;

        // Accuracy = 0 is almost always fake
        if (accuracies.some(a => a === 0)) {
            this.reasons.push('Akurasi GPS = 0 meter (tidak mungkin pada GPS asli)');
            return { score: 1.5 };
        }

        // Accuracy < 1m is very suspicious
        if (avgAccuracy < this.options.minSuspiciousAccuracy) {
            this.reasons.push(`Akurasi GPS terlalu sempurna: ${avgAccuracy.toFixed(1)}m (GPS asli biasanya > 3m)`);
            return { score: 1.2 };
        }

        // All accuracies exactly identical is suspicious (real GPS fluctuates)
        if (this.samples.length > 1) {
            const allSame = accuracies.every(a => a === accuracies[0]);
            if (allSame && accuracies[0] < 10) {
                this.reasons.push(`Semua sampel memiliki akurasi identik: ${accuracies[0]}m (tidak alami)`);
                return { score: 0.8 };
            }
        }

        return { score: 0 };
    }

    _checkTeleportation() {
        for (let i = 1; i < this.samples.length; i++) {
            const prev = this.samples[i - 1];
            const curr = this.samples[i];
            const distance = this._haversineDistance(
                prev.latitude, prev.longitude,
                curr.latitude, curr.longitude
            );
            const timeDiff = curr.timestamp - prev.timestamp;

            // Teleportation: large distance in very short time
            if (distance > this.options.teleportThreshold && timeDiff < this.options.teleportTimeWindow) {
                this.reasons.push(`Perpindahan GPS tidak wajar: ${distance.toFixed(0)}m dalam ${(timeDiff / 1000).toFixed(1)}s`);
                return { score: 1.5 };
            }
        }
        return { score: 0 };
    }

    _checkTimestamps() {
        for (let i = 1; i < this.samples.length; i++) {
            const timeDiff = this.samples[i].timestamp - this.samples[i - 1].timestamp;

            // Timestamps going backwards or exactly zero diff
            if (timeDiff <= 0) {
                this.reasons.push('Timestamp GPS tidak konsisten (mundur atau identik)');
                return { score: 1 };
            }

            // Timestamps too perfectly spaced (within 1ms precision)
            if (timeDiff % 1000 === 0 && this.samples.length > 2) {
                this.reasons.push('Timestamp GPS terlalu presisi (mencurigakan)');
                return { score: 0.5 };
            }
        }
        return { score: 0 };
    }

    _checkConsistency() {
        if (this.samples.length < 2) return { score: 0 };

        // Check if ALL coordinates are perfectly identical (GPS always has micro-variation)
        const allIdentical = this.samples.every(s =>
            s.latitude === this.samples[0].latitude &&
            s.longitude === this.samples[0].longitude
        );

        if (allIdentical && this.samples.length >= 3) {
            this.reasons.push('Semua sampel GPS identik secara sempurna (GPS asli selalu memiliki variasi mikro)');
            return { score: 1 };
        }

        return { score: 0 };
    }

    _checkSensors() {
        // If device has motion sensors but reported no movement,
        // while GPS position is stable — could be normal (device sitting still)
        // This is a weak signal, only flag if combined with other factors

        if (!this.sensorData.hasMotion && !this.sensorData.hasOrientation) {
            // No sensor APIs — could be desktop pretending to be mobile
            this.reasons.push('Sensor gerak perangkat tidak terdeteksi');
            return { score: 0.5 };
        }

        return { score: 0 };
    }

    _checkMissingData() {
        const lastSample = this.samples[this.samples.length - 1];

        // Mock locations often have null altitude
        let score = 0;
        const issues = [];

        if (lastSample.altitude === null && lastSample.accuracy < 20) {
            issues.push('altitude null');
            score += 0.3;
        }

        if (lastSample.speed === null && lastSample.heading === null && lastSample.altitude === null) {
            issues.push('speed/heading/altitude semua null');
            score += 0.4;
        }

        if (issues.length > 0) {
            this.reasons.push(`Data GPS tidak lengkap (${issues.join(', ')}) — indikasi lokasi virtual`);
        }

        return { score };
    }

    _startSensorMonitoring() {
        // Check DeviceMotion
        this._motionHandler = (event) => {
            this.sensorData.hasMotion = true;
            const acc = event.acceleration || {};
            if (Math.abs(acc.x || 0) > 0.5 || Math.abs(acc.y || 0) > 0.5 || Math.abs(acc.z || 0) > 0.5) {
                this.sensorData.motionDetected = true;
            }
        };

        // Check DeviceOrientation
        this._orientationHandler = (event) => {
            this.sensorData.hasOrientation = true;
        };

        try {
            window.addEventListener('devicemotion', this._motionHandler, true);
            window.addEventListener('deviceorientation', this._orientationHandler, true);
        } catch (e) {
            // Sensors not supported
        }
    }

    _stopSensorMonitoring() {
        try {
            window.removeEventListener('devicemotion', this._motionHandler, true);
            window.removeEventListener('deviceorientation', this._orientationHandler, true);
        } catch (e) {
            // Ignore
        }
    }

    _haversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = this._toRad(lat2 - lat1);
        const dLon = this._toRad(lon2 - lon1);
        const a = Math.sin(dLat / 2) ** 2 +
                  Math.cos(this._toRad(lat1)) * Math.cos(this._toRad(lat2)) *
                  Math.sin(dLon / 2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    _toRad(deg) {
        return deg * Math.PI / 180;
    }

    _sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    _updateStatus(message) {
        if (typeof this.onStatusUpdate === 'function') {
            this.onStatusUpdate(message);
        }
    }
}

// Export for use
window.MockLocationDetector = MockLocationDetector;
