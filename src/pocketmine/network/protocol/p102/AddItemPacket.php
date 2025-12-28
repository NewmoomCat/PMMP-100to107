<?php

namespace pocketmine\network\protocol\p102;
#include <rules/DataPacket.h>
use pocketmine\network\protocol\DataPacket;

class AddItemPacket extends DataPacket
{
	const NETWORK_ID = Info::ADD_ITEM_PACKET;

	public $item;

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putSlot($this->item);
	}

}