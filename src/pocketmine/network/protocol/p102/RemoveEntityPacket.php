<?php

namespace pocketmine\network\protocol\p102;
#include <rules/DataPacket.h>
use pocketmine\network\protocol\DataPacket;

class RemoveEntityPacket extends DataPacket
{
	const NETWORK_ID = Info::REMOVE_ENTITY_PACKET;

	public $eid;

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putEntityId($this->eid);
	}

}