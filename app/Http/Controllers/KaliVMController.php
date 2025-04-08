<?php

namespace App\Http\Controllers;

use App\Services\ProxmoxService;
use App\Services\NetworkManager;
use Illuminate\Http\Request;

class KaliVMController extends Controller
{
    private $proxmoxService;
    private $networkManager;

    public function __construct(ProxmoxService $proxmoxService, NetworkManager $networkManager)
    {
        $this->proxmoxService = $proxmoxService;
        $this->networkManager = $networkManager;
    }

    public function setupAttackEnvironment(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'iso_path' => 'required|string',
            'public_ip' => 'required|ip'
        ]);

        try {
            $this->proxmoxService->authenticate();

            // Create attack network
            $attackNetwork = $this->networkManager->getNetworkConfig('attack');
            $this->proxmoxService->createNetwork('attack_net', $attackNetwork['network']);

            // Create target network
            $targetNetwork = $this->networkManager->getNetworkConfig('target');
            $this->proxmoxService->createNetwork('target_net', $targetNetwork['network']);

            // Create Kali VM
            $vm = $this->proxmoxService->createKaliVM($request->name, $request->iso_path);

            // Configure networking for Kali VM
            $attackIP = $this->networkManager->generateAttackIP();
            $this->proxmoxService->configureNetworking($vm->vmid, [
                'ip' => $attackIP,
                'gateway' => $attackNetwork['gateway']
            ]);

            // Configure VNC access with public IP
            $vncConfig = $this->proxmoxService->configureVNCAccess(
                $vm->vmid, 
                $request->public_ip
            );

            return response()->json([
                'success' => true,
                'vm' => $vm,
                'network' => [
                    'attack_ip' => $attackIP,
                    'attack_network' => $attackNetwork['network'],
                ],
                'vnc' => [
                    'host' => $vncConfig['ip'],
                    'port' => $vncConfig['port'],
                    'password' => $vncConfig['password']
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function teardown($vmid)
    {
        try {
            $this->proxmoxService->authenticate();
            $this->proxmoxService->deleteVM($vmid);
            
            return response()->json(['success' => true, 'message' => 'Environment destroyed successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'iso_path' => 'required|string'
        ]);

        try {
            $this->proxmoxService->authenticate();
            $vm = $this->proxmoxService->createKaliVM($request->name, $request->iso_path);
            return response()->json(['success' => true, 'vm' => $vm]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function start($vmid)
    {
        try {
            $this->proxmoxService->authenticate();
            $result = $this->proxmoxService->startVM($vmid);
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getVNCConnection($vmid)
    {
        try {
            $this->proxmoxService->authenticate();
            $vncDetails = $this->proxmoxService->getVNCDetails($vmid);
            return response()->json(['success' => true, 'vnc' => $vncDetails]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
} 