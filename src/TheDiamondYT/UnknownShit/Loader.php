<?php

namespace TheDiamondYT\UnknownShit;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\utils\TextFormat as TF;

class Loader extends PluginBase implements Listener {

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    
    /**
     * @priority HIGHEST
     */
    public function onPacketReceive(DataPacketReceiveEvent $ev) {
        $packet = $ev->getPacket();
        $player = $ev->getPlayer();
        if($packet instanceof LoginPacket) {
            $this->getLogger()->info(TF::DARK_AQUA . sprintf("Player %s joined with protocol: %s. Server protocol: %s. Client data:",
                $packet->username, 
                $packet->protocol,
                ProtocolInfo::CURRENT_PROTOCOL
            ));
            foreach($packet->clientData as $key => $value) {
                if($key !== "SkinData") {
                    $this->getLogger()->info(TF::AQUA . "$key  => $value");
                }
            }   
            $this->getLogger()->info("");   
            $this->getLogger()->info(TF::DARK_AQUA . "More info:");       
            $extraData = [
                "gameEdition" => $packet->gameEdition,
                "clientId" => $packet->clientId,
                "clientUUID" => $packet->clientUUID,
                "identityPublicKey" => $packet->identityPublicKey    
            ];
            foreach($extraData as $key => $value) {
                $this->getLogger()->info(TF::AQUA . "$key => $value");
            }
        }
        if($packet instanceof PlayerActionPacket) {
			$knownActions = (new \ReflectionClass("\\pocketmine\\network\\mcpe\\protocol\\PlayerActionPacket"))->getConstants();
			
			foreach($knownActions as $name => $value) {
				if($packet->action == $value) {
					return;
				}
			}
			$this->getLogger()->info(TF::GOLD . "Unknown player action: " . TF::WHITE . $packet->action);
		}
    }
}
