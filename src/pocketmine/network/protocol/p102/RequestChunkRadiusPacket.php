<?php

namespace pocketmine\network\protocol\p102;
#include <rules/DataPacket.h>
use pocketmine\network\protocol\DataPacket;

class RequestChunkRadiusPacket extends DataPacket
{
	const NETWORK_ID = Info::REQUEST_CHUNK_RADIUS_PACKET;

	public $radius;

	public function decode(){
		$this->radius = $this->getVarInt();
	}

	public function encode(){

	}
}