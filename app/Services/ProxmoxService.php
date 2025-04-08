<?php

namespace App\Services;

use GuzzleHttp\Client;
use Exception;

class ProxmoxService
{
    private $client;
    private $baseUrl;
    private $ticket;

    public function __construct()
    {
        $this->baseUrl = config('proxmox.api_url');
        $this->client = new Client([
            'verify' => false, // Only if using self-signed certificates
            'base_uri' => $this->baseUrl
        ]);
    }

    public function authenticate()
    {
        try {
            $response = $this->client->post('access/ticket', [
                'form_params' => [
                    'username' => config('proxmox.username'),
                    'password' => config('proxmox.password'),
                ]
            ]);

            $result = json_decode($response->getBody()->getContents());
            $this->ticket = $result->data->ticket;
            
            return $this->ticket;
        } catch (Exception $e) {
            throw new Exception('Proxmox authentication failed: ' . $e->getMessage());
        }
    }

    public function createKaliVM($name, $isoPath)
    {
        try {
            $response = $this->client->post("nodes/proxmox/qemu", [
                'headers' => [
                    'Cookie' => 'PVEAuthCookie=' . $this->ticket
                ],
                'form_params' => [
                    'vmid' => $this->getNextVMID(),
                    'name' => $name,
                    'ide2' => $isoPath . ',media=cdrom',
                    'ostype' => 'l26', // Linux 2.6+ kernel
                    'memory' => 2048,
                    'cores' => 2,
                    'net0' => 'virtio,bridge=vmbr0',
                    'boot' => 'order=ide2;net0',
                ]
            ]);

            return json_decode($response->getBody()->getContents());
        } catch (Exception $e) {
            throw new Exception('Failed to create Kali VM: ' . $e->getMessage());
        }
    }

    private function getNextVMID()
    {
        // Implementation to get next available VMID
        // You should implement this based on your Proxmox setup
        return 100; // Example
    }

    public function startVM($vmid)
    {
        try {
            $response = $this->client->post("nodes/proxmox/qemu/$vmid/status/start", [
                'headers' => [
                    'Cookie' => 'PVEAuthCookie=' . $this->ticket
                ]
            ]);

            return json_decode($response->getBody()->getContents());
        } catch (Exception $e) {
            throw new Exception('Failed to start VM: ' . $e->getMessage());
        }
    }

    public function getVNCDetails($vmid)
    {
        try {
            $response = $this->client->get("nodes/proxmox/qemu/$vmid/config", [
                'headers' => [
                    'Cookie' => 'PVEAuthCookie=' . $this->ticket
                ]
            ]);

            $config = json_decode($response->getBody()->getContents());
            return [
                'port' => $config->data->vncport,
                'password' => $config->data->vncpassword
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to get VNC details: ' . $e->getMessage());
        }
    }

    public function configureNetworking($vmid, $ipConfig)
    {
        try {
            // Configure network interface with specific IP
            $response = $this->client->put("nodes/proxmox/qemu/$vmid/config", [
                'headers' => [
                    'Cookie' => 'PVEAuthCookie=' . $this->ticket
                ],
                'form_params' => [
                    'ipconfig0' => "ip=${ipConfig['ip']}/24,gw=${ipConfig['gateway']}"
                ]
            ]);

            return json_decode($response->getBody()->getContents());
        } catch (Exception $e) {
            throw new Exception('Failed to configure networking: ' . $e->getMessage());
        }
    }

    public function createNetwork($name, $subnet)
    {
        try {
            $response = $this->client->post("nodes/proxmox/network", [
                'headers' => [
                    'Cookie' => 'PVEAuthCookie=' . $this->ticket
                ],
                'form_params' => [
                    'type' => 'bridge',
                    'iface' => $name,
                    'autostart' => 1,
                    'cidr' => $subnet,
                ]
            ]);

            return json_decode($response->getBody()->getContents());
        } catch (Exception $e) {
            throw new Exception('Failed to create network: ' . $e->getMessage());
        }
    }

    public function deleteVM($vmid)
    {
        try {
            $response = $this->client->delete("nodes/proxmox/qemu/$vmid", [
                'headers' => [
                    'Cookie' => 'PVEAuthCookie=' . $this->ticket
                ]
            ]);

            return json_decode($response->getBody()->getContents());
        } catch (Exception $e) {
            throw new Exception('Failed to delete VM: ' . $e->getMessage());
        }
    }

    public function configureVNCAccess($vmid, $publicIP)
    {
        try {
            // Generate a secure VNC password
            $vncPassword = bin2hex(random_bytes(8));
            
            $response = $this->client->put("nodes/proxmox/qemu/$vmid/config", [
                'headers' => [
                    'Cookie' => 'PVEAuthCookie=' . $this->ticket
                ],
                'form_params' => [
                    'vncserver' => $publicIP . ':0', // Bind to public IP
                    'vncpassword' => $vncPassword,
                    'vncport' => $this->allocateVNCPort(), // Allocate a free port
                    'args' => '-vnc ' . $publicIP . ':{port}', // Direct VNC binding
                ]
            ]);

            return [
                'success' => true,
                'ip' => $publicIP,
                'port' => $this->getVNCPort($vmid),
                'password' => $vncPassword
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to configure VNC access: ' . $e->getMessage());
        }
    }

    private function allocateVNCPort()
    {
        // Start from port 5900 (VNC standard)
        $basePort = 5900;
        $maxPort = 5999;
        
        try {
            // Get list of used ports
            $response = $this->client->get("nodes/proxmox/qemu", [
                'headers' => [
                    'Cookie' => 'PVEAuthCookie=' . $this->ticket
                ]
            ]);
            
            $vms = json_decode($response->getBody()->getContents());
            $usedPorts = [];
            
            foreach ($vms->data as $vm) {
                if (isset($vm->vncport)) {
                    $usedPorts[] = $vm->vncport;
                }
            }
            
            // Find first available port
            for ($port = $basePort; $port <= $maxPort; $port++) {
                if (!in_array($port, $usedPorts)) {
                    return $port;
                }
            }
            
            throw new Exception('No available VNC ports');
        } catch (Exception $e) {
            throw new Exception('Failed to allocate VNC port: ' . $e->getMessage());
        }
    }

    public function getVNCPort($vmid)
    {
        try {
            $response = $this->client->get("nodes/proxmox/qemu/$vmid/config", [
                'headers' => [
                    'Cookie' => 'PVEAuthCookie=' . $this->ticket
                ]
            ]);

            $config = json_decode($response->getBody()->getContents());
            return $config->data->vncport ?? null;
        } catch (Exception $e) {
            throw new Exception('Failed to get VNC port: ' . $e->getMessage());
        }
    }
} 