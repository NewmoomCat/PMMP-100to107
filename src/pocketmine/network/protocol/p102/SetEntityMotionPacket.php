<?php

namespace pocketmine\network\protocol\p102;
#include <rules/DataPacket.h>
use pocketmine\network\protocol\DataPacket;

class SetEntityMotionPacket extends DataPacket
{
	const NETWORK_ID = Info::SET_ENTITY_MOTION_PACKET;

	public $eid;
	public $motionX;
	public $motionY;
	public $motionZ;

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putEntityId($this->eid);
		$this->putVector3f($this->motionX, $this->motionY, $this->motionZ);
	}

}