<?php

namespace pocketmine\network\protocol\p102;
#include <rules/DataPacket.h>
use pocketmine\network\protocol\DataPacket;

class PlayerFallPacket extends DataPacket
{
	const NETWORK_ID = Info::PLAYER_FALL_PACKET;

	public $fallDistance;

	public function decode(){
		$this->fallDistance = $this->getLFloat();
	}

	public function encode(){

	}
}