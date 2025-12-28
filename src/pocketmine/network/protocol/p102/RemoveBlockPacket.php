<?php

namespace pocketmine\network\protocol\p102;
#include <rules/DataPacket.h>
use pocketmine\network\protocol\DataPacket;

class RemoveBlockPacket extends DataPacket
{
	const NETWORK_ID = Info::REMOVE_BLOCK_PACKET;

	public $x;
	public $y;
	public $z;

	public function decode(){
		$this->getBlockCoords($this->x, $this->y, $this->z);
	}

	public function encode(){

	}

}