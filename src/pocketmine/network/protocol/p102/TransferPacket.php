<?php

namespace pocketmine\network\protocol\p102;
#include <rules/DataPacket.h>
use pocketmine\network\protocol\DataPacket;

class TransferPacket extends DataPacket
{
	const NETWORK_ID = Info::TRANSFER_PACKET;

	public $address;
	public $port = 19132;

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putString($this->address);
		$this->putLShort($this->port);
	}

}