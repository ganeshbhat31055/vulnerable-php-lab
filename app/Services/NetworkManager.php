<?php

namespace App\Services;

class NetworkManager
{
    private $attackNetwork = '192.168.10.0/24';
    private $targetNetwork = '192.168.20.0/24';

    public function generateAttackIP()
    {
        // Generate a random IP in the attack network range
        return '192.168.10.' . rand(10, 250);
    }

    public function generateTargetIP()
    {
        // Generate a random IP in the target network range
        return '192.168.20.' . rand(10, 250);
    }

    public function getNetworkConfig($type)
    {
        $configs = [
            'attack' => [
                'network' => $this->attackNetwork,
                'gateway' => '192.168.10.1',
                'bridge' => 'vmbr1'
            ],
            'target' => [
                'network' => $this->targetNetwork,
                'gateway' => '192.168.20.1',
                'bridge' => 'vmbr2'
            ]
        ];

        return $configs[$type] ?? null;
    }
} 