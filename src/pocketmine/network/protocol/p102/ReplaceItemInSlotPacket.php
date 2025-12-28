<?php

namespace pocketmine\network\protocol\p102;
#include <rules/DataPacket.h>
use pocketmine\network\protocol\DataPacket;

class ReplaceItemInSlotPacket extends DataPacket
{
	const NETWORK_ID = Info::REPLACE_ITEM_IN_SLOT_PACKET;

	public $item;

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putSlot($this->item);
	}

}