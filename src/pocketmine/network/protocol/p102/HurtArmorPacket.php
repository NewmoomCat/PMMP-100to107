<?php

namespace pocketmine\network\protocol\p102;
#include <rules/DataPacket.h>
use pocketmine\network\protocol\DataPacket;

class HurtArmorPacket extends DataPacket
{
	const NETWORK_ID = Info::HURT_ARMOR_PACKET;

	public $health;

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putVarInt($this->health);
	}

}