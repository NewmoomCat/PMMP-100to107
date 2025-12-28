<?php

namespace pocketmine\network\protocol\p102;
#include <rules/DataPacket.h>
use pocketmine\network\protocol\DataPacket;

class SetPlayerGameTypePacket extends DataPacket
{
	const NETWORK_ID = Info::SET_PLAYER_GAME_TYPE_PACKET;

	public $gamemode;

	public function decode(){
		$this->gamemode = $this->getVarInt();
	}

	public function encode(){
		$this->reset();
		$this->putVarInt($this->gamemode);
	}
}