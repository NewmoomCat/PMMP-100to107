<?php

namespace pocketmine\network\protocol\p102;
#include <rules/DataPacket.h>
use pocketmine\network\protocol\DataPacket;

class ContainerSetDataPacket extends DataPacket
{
	const NETWORK_ID = Info::CONTAINER_SET_DATA_PACKET;

	public $windowid;
	public $property;
	public $value;

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putByte($this->windowid);
		$this->putVarInt($this->property);
		$this->putVarInt($this->value);
	}

}