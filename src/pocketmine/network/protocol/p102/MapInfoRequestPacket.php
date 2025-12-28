<?php

namespace pocketmine\network\protocol\p102;

use pocketmine\network\protocol\DataPacket;

class MapInfoRequestPacket extends DataPacket
{
	const NETWORK_ID = Info::MAP_INFO_REQUEST_PACKET;

	public $uuid;

	public function decode(){
		$this->uuid = $this->getEntityId();
	}

	public function encode(){
		$this->reset();
		$this->putEntityId($this->uuid);
	}

}