<?php

namespace pocketmine\network\protocol\p102;
#include <rules/DataPacket.h>
use pocketmine\network\protocol\DataPacket;

class DropItemPacket extends DataPacket
{
	const NETWORK_ID = Info::DROP_ITEM_PACKET;

	public $type;
	public $item;

	public function decode(){
		$this->type = $this->getByte();
		$this->item = $this->getSlot();
	}

	public function encode(){

	}

}