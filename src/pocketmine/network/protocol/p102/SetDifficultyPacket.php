<?php

namespace pocketmine\network\protocol\p102;
#include <rules/DataPacket.h>
use pocketmine\network\protocol\DataPacket;

class SetDifficultyPacket extends DataPacket
{
	const NETWORK_ID = Info::SET_DIFFICULTY_PACKET;

	public $difficulty;

	public function decode(){
		$this->difficulty = $this->getUnsignedVarInt();
	}

	public function encode(){
		$this->reset();
		$this->putUnsignedVarInt($this->difficulty);
	}

}