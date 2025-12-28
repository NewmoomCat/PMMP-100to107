<?php

namespace pocketmine\network\protocol\p102;
#include <rules/DataPacket.h>
use pocketmine\network\protocol\DataPacket;

class DisconnectPacket extends DataPacket
{
	const NETWORK_ID = Info::DISCONNECT_PACKET;

	public $hideDisconnectionScreen = false;
	public $message;

	public function decode(){
		$this->hideDisconnectionScreen = $this->getBool();
		$this->message = $this->getString();
	}

	public function encode(){
		$this->reset();
		$this->putBool($this->hideDisconnectionScreen);
		$this->putString($this->message);
	}
}