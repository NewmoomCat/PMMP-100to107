<?php

namespace pocketmine\network\protocol\p102;
#include <rules/DataPacket.h>
use pocketmine\network\protocol\DataPacket;

class ContainerClosePacket extends DataPacket
{
	const NETWORK_ID = Info::CONTAINER_CLOSE_PACKET;

	public $windowid;

	public function decode(){
		$this->windowid = $this->getByte();
	}

	public function encode(){
		$this->reset();
		$this->putByte($this->windowid);
	}

}