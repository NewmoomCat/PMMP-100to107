<?php

namespace pocketmine\network\protocol\p102;
#include <rules/DataPacket.h>
use pocketmine\network\protocol\DataPacket;

class SetHealthPacket extends DataPacket
{
	const NETWORK_ID = Info::SET_HEALTH_PACKET;

	public $health;

	public function decode(){
		$this->health = $this->getVarInt();
	}

	public function encode(){
		$this->reset();
		$this->putVarInt($this->health);
	}

}