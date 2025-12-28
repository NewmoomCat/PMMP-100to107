<?php

namespace pocketmine\network\protocol\p102;
#include <rules/DataPacket.h>
use pocketmine\network\protocol\DataPacket;

class TakeItemEntityPacket extends DataPacket
{
	const NETWORK_ID = Info::TAKE_ITEM_ENTITY_PACKET;

	public $target;
	public $eid;

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putEntityId($this->target);
		$this->putEntityId($this->eid);
	}

}