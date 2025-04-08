<template>
    <div class="vnc-container">
        <div v-if="!connected" class="controls">
            <button @click="connectVNC">Connect to Kali Linux</button>
        </div>
        <div id="vnc-display"></div>
    </div>
</template>

<script>
import { RFB } from '@novnc/novnc';

export default {
    name: 'KaliVNC',
    props: {
        vmid: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            connected: false,
            rfb: null
        }
    },
    methods: {
        async connectVNC() {
            try {
                const response = await axios.get(`/api/kali/${this.vmid}/vnc`);
                const { port, password } = response.data.vnc;
                
                // Create WebSocket URL for noVNC
                const wsUrl = `ws://${window.location.hostname}:${port}`;
                
                this.rfb = new RFB(document.getElementById('vnc-display'), wsUrl, {
                    credentials: { password }
                });
                
                this.rfb.addEventListener('connect', () => {
                    this.connected = true;
                });
                
                this.rfb.addEventListener('disconnect', () => {
                    this.connected = false;
                });
            } catch (error) {
                console.error('Failed to connect to VNC:', error);
            }
        }
    },
    beforeDestroy() {
        if (this.rfb) {
            this.rfb.disconnect();
        }
    }
}
</script>

<style scoped>
.vnc-container {
    width: 100%;
    height: 100%;
    min-height: 600px;
}

#vnc-display {
    width: 100%;
    height: 100%;
}

.controls {
    margin-bottom: 1rem;
}
</style> 