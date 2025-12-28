<?php

namespace pocketmine\network\protocol\p102;
#include <rules/DataPacket.h>
use pocketmine\network\protocol\DataPacket;

class ChunkRadiusUpdatedPacket extends DataPacket
{
	const NETWORK_ID = Info::CHUNK_RADIUS_UPDATED_PACKET;

	public $radius;

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putVarInt($this->radius);
	}
}