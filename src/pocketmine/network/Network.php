<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

/**
 * Network-related classes
 */
namespace pocketmine\network;

use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\AddHangingEntityPacket;
use pocketmine\network\protocol\AddItemEntityPacket;
use pocketmine\network\protocol\AddItemPacket;
use pocketmine\network\protocol\AddPaintingPacket;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\AdventureSettingsPacket;
use pocketmine\network\protocol\AnimatePacket;
use pocketmine\network\protocol\AvailableCommandsPacket;
use pocketmine\network\protocol\BatchPacket;
use pocketmine\network\protocol\BlockEntityDataPacket;
use pocketmine\network\protocol\BlockEventPacket;
use pocketmine\network\protocol\BossEventPacket; 
use pocketmine\network\protocol\BlockPickRequestPacket;
use pocketmine\network\protocol\ChangeDimensionPacket;
use pocketmine\network\protocol\ChunkRadiusUpdatedPacket;
use pocketmine\network\protocol\ClientboundMapItemDataPacket;
use pocketmine\network\protocol\ClientToServerHandshakePacket;
use pocketmine\network\protocol\CommandBlockUpdatePacket;
use pocketmine\network\protocol\CommandStepPacket;
use pocketmine\network\protocol\ContainerClosePacket;
use pocketmine\network\protocol\ContainerOpenPacket;
use pocketmine\network\protocol\ContainerSetContentPacket;
use pocketmine\network\protocol\ContainerSetDataPacket;
use pocketmine\network\protocol\ContainerSetSlotPacket;
use pocketmine\network\protocol\CraftingDataPacket;
use pocketmine\network\protocol\CraftingEventPacket;
use pocketmine\network\protocol\CameraPacket;
use pocketmine\network\protocol\DataPacket;
use pocketmine\network\protocol\DisconnectPacket;
use pocketmine\network\protocol\DropItemPacket;
use pocketmine\network\protocol\EntityEventPacket;
use pocketmine\network\protocol\ExplodePacket;
use pocketmine\network\protocol\FullChunkDataPacket;
use pocketmine\network\protocol\HurtArmorPacket;
use pocketmine\network\protocol\Info;
use pocketmine\network\protocol\Info as ProtocolInfo;
use pocketmine\network\protocol\InteractPacket;
use pocketmine\network\protocol\InventoryActionPacket;
use pocketmine\network\protocol\ItemFrameDropItemPacket;
use pocketmine\network\protocol\LevelEventPacket;
use pocketmine\network\protocol\LevelSoundEventPacket;
use pocketmine\network\protocol\LoginPacket;
use pocketmine\network\protocol\MapInfoRequestPacket;
use pocketmine\network\protocol\MobArmorEquipmentPacket;
use pocketmine\network\protocol\MobEquipmentPacket;
use pocketmine\network\protocol\MoveEntityPacket;
use pocketmine\network\protocol\MovePlayerPacket;
use pocketmine\network\protocol\PlaySoundPacket;
use pocketmine\network\protocol\PlayStatusPacket;
use pocketmine\network\protocol\PlayerActionPacket;
use pocketmine\network\protocol\PlayerFallPacket;
use pocketmine\network\protocol\PlayerInputPacket;
use pocketmine\network\protocol\PlayerListPacket;
use pocketmine\network\protocol\RemoveBlockPacket;
use pocketmine\network\protocol\RemoveEntityPacket;
use pocketmine\network\protocol\ReplaceItemInSlotPacket;
use pocketmine\network\protocol\RequestChunkRadiusPacket;
use pocketmine\network\protocol\ResourcePackChunkDataPacket;
use pocketmine\network\protocol\ResourcePackChunkRequestPacket;
use pocketmine\network\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\protocol\ResourcePackDataInfoPacket;
use pocketmine\network\protocol\ResourcePacksInfoPacket;
use pocketmine\network\protocol\ResourcePackStackPacket;
use pocketmine\network\protocol\RespawnPacket;
use pocketmine\network\protocol\RiderJumpPacket;
use pocketmine\network\protocol\SetCommandsEnabledPacket;
use pocketmine\network\protocol\SetDifficultyPacket;
use pocketmine\network\protocol\SetEntityDataPacket;
use pocketmine\network\protocol\SetEntityLinkPacket;
use pocketmine\network\protocol\SetEntityMotionPacket;
use pocketmine\network\protocol\SetHealthPacket;
use pocketmine\network\protocol\SetPlayerGameTypePacket;
use pocketmine\network\protocol\SetSpawnPositionPacket;
use pocketmine\network\protocol\SetTimePacket;
use pocketmine\network\protocol\SetTitlePacket;
use pocketmine\network\protocol\ServerToClientHandshakePacket;
use pocketmine\network\protocol\ShowCreditsPacket;
use pocketmine\network\protocol\SpawnExperienceOrbPacket;
use pocketmine\network\protocol\StartGamePacket;
use pocketmine\network\protocol\StopSoundPacket;
use pocketmine\network\protocol\TakeItemEntityPacket;
use pocketmine\network\protocol\TextPacket;
use pocketmine\network\protocol\TransferPacket;
use pocketmine\network\protocol\UpdateBlockPacket;
use pocketmine\network\protocol\UpdateTradePacket;
use pocketmine\network\protocol\UseItemPacket;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\BinaryStream;
use pocketmine\utils\MainLogger;

class Network {

	public static $BATCH_THRESHOLD = 512;

	/** @var \SplFixedArray */
	private $packetPool;

	/** @var Server */
	private $server;

	/** @var SourceInterface[] */
	private $interfaces = [];

	/** @var AdvancedSourceInterface[] */
	private $advancedInterfaces = [];

	private $upload = 0;
	private $download = 0;

	private $name;

	public function __construct(Server $server) {

		$this->registerPackets();

		$this->server = $server;
	}

	public function addStatistics($upload, $download) {
		$this->upload += $upload;
		$this->download += $download;
	}

	public function getUpload() {
		return $this->upload;
	}

	public function getDownload() {
		return $this->download;
	}

	public function resetStatistics() {
		$this->upload = 0;
		$this->download = 0;
	}

	/**
	 * @return SourceInterface[]
	 */
	public function getInterfaces() {
		return $this->interfaces;
	}

	public function processInterfaces() {
		foreach ($this->interfaces as $interface) {
			try {
				$interface->process();
			} catch (\Throwable $e) {
				$logger = $this->server->getLogger();
				if (\pocketmine\DEBUG > 1) {
					if ($logger instanceof MainLogger) {
						$logger->logException($e);
					}
				}

				$interface->emergencyShutdown();
				$this->unregisterInterface($interface);
				$logger->critical($this->server->getLanguage()->translateString("pocketmine.server.networkError", [get_class($interface), $e->getMessage()]));
			}
		}
	}

	/**
	 * @param SourceInterface $interface
	 */
	public function registerInterface(SourceInterface $interface) {
		$this->interfaces[$hash = spl_object_hash($interface)] = $interface;
		if ($interface instanceof AdvancedSourceInterface) {
			$this->advancedInterfaces[$hash] = $interface;
			$interface->setNetwork($this);
		}
		$interface->setName($this->name);
	}

	/**
	 * @param SourceInterface $interface
	 */
	public function unregisterInterface(SourceInterface $interface) {
		unset($this->interfaces[$hash = spl_object_hash($interface)],
			$this->advancedInterfaces[$hash]);
	}

	/**
	 * Sets the server name shown on each interface Query
	 *
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = (string)$name;
		foreach ($this->interfaces as $interface) {
			$interface->setName($this->name);
		}
	}

	public function getName() {
		return $this->name;
	}

	public function updateName() {
		foreach ($this->interfaces as $interface) {
			$interface->setName($this->name);
		}
	}

	/**
	 * @param int        $id 0-255
	 * @param DataPacket $class
	 */
	public function registerPacket($id, $class) {
		$this->packetPool[$id] = new $class;
	}

	public function getServer() {
		return $this->server;
	}

	public function processBatch(BatchPacket $packet, Player $p){
		try{
			if(strlen($packet->payload) === 0){
				//prevent zlib_decode errors for incorrectly-decoded packets
				throw new \InvalidArgumentException("BatchPacket payload is empty or packet decode error");
			}

			$str = zlib_decode($packet->payload, 1024 * 1024 * 64); //Max 64MB
			$len = strlen($str);

			if($len === 0){
				throw new \InvalidStateException("Decoded BatchPacket payload is empty");
			}

			$stream = new BinaryStream($str);

			while($stream->offset < $len){
				$buf = $stream->getString();
				if(($pk = $this->getPacket(ord($buf[0]))) !== null){
					if($pk::NETWORK_ID === Info::BATCH_PACKET){
						throw new \InvalidStateException("Invalid BatchPacket inside BatchPacket");
					}

					$pk->setBuffer($buf, 1);

					$pk->decode();
					assert($pk->feof(), "Still " . strlen(substr($pk->buffer, $pk->offset)) . " bytes unread in " . get_class($pk));
					$p->handleDataPacket($pk);
				}
			}
		}catch(\Throwable $e){
			if(\pocketmine\DEBUG > 1){
				$logger = $this->server->getLogger();
				if($logger instanceof MainLogger){
					$logger->debug("BatchPacket " . " 0x" . bin2hex($packet->payload));
					$logger->logException($e);
				}
			}
		}
	}

	/**
	 * @param $id
	 *
	 * @return DataPacket
	 */
	public function getPacket($id) {
		/** @var DataPacket $class */
		$class = $this->packetPool[$id];
		if ($class !== null) {
			return clone $class;
		}
		return null;
	}


	/**
	 * @param string $address
	 * @param int    $port
	 * @param string $payload
	 */
	public function sendPacket($address, $port, $payload) {
		foreach ($this->advancedInterfaces as $interface) {
			$interface->sendRawPacket($address, $port, $payload);
		}
	}

	/**
	 * Blocks an IP address from the main interface. Setting timeout to -1 will block it forever
	 *
	 * @param string $address
	 * @param int    $timeout
	 */
	public function blockAddress($address, $timeout = 300) {
		foreach ($this->advancedInterfaces as $interface) {
			$interface->blockAddress($address, $timeout);
		}
	}

	/**
	 * Unblocks an IP address from the main interface.
	 *
	 * @param string $address
	 */
	public function unblockAddress($address) {
		foreach ($this->advancedInterfaces as $interface) {
			$interface->unblockAddress($address);
		}
	}

	private function registerPackets() {
		$this->packetPool = new \SplFixedArray(256);

		$this->registerPacket(ProtocolInfo::ADD_ENTITY_PACKET, AddEntityPacket::class);
		$this->registerPacket(ProtocolInfo::ADD_HANGING_ENTITY_PACKET, AddHangingEntityPacket::class);
		$this->registerPacket(ProtocolInfo::ADD_ITEM_ENTITY_PACKET, AddItemEntityPacket::class);
		$this->registerPacket(ProtocolInfo::ADD_ITEM_PACKET, AddItemPacket::class);
		$this->registerPacket(ProtocolInfo::ADD_PAINTING_PACKET, AddPaintingPacket::class);
		$this->registerPacket(ProtocolInfo::ADD_PLAYER_PACKET, AddPlayerPacket::class);
		$this->registerPacket(ProtocolInfo::ADVENTURE_SETTINGS_PACKET, AdventureSettingsPacket::class);
		$this->registerPacket(ProtocolInfo::ANIMATE_PACKET, AnimatePacket::class);
		$this->registerPacket(ProtocolInfo::AVAILABLE_COMMANDS_PACKET, AvailableCommandsPacket::class);
		$this->registerPacket(ProtocolInfo::BATCH_PACKET, BatchPacket::class);
		$this->registerPacket(ProtocolInfo::BLOCK_ENTITY_DATA_PACKET, BlockEntityDataPacket::class);
		$this->registerPacket(ProtocolInfo::BLOCK_EVENT_PACKET, BlockEventPacket::class);
 		$this->registerPacket(ProtocolInfo::BOSS_EVENT_PACKET, BossEventPacket::class);
		$this->registerPacket(ProtocolInfo::CAMERA_PACKET, CameraPacket::class);
		$this->registerPacket(ProtocolInfo::CHANGE_DIMENSION_PACKET, ChangeDimensionPacket::class);
		$this->registerPacket(ProtocolInfo::CHUNK_RADIUS_UPDATED_PACKET, ChunkRadiusUpdatedPacket::class);
		$this->registerPacket(ProtocolInfo::CLIENTBOUND_MAP_ITEM_DATA_PACKET, ClientboundMapItemDataPacket::class);
		$this->registerPacket(ProtocolInfo::CLIENT_TO_SERVER_HANDSHAKE_PACKET, ClientToServerHandshakePacket::class);
		$this->registerPacket(ProtocolInfo::COMMAND_STEP_PACKET, CommandStepPacket::class);
		$this->registerPacket(ProtocolInfo::CONTAINER_CLOSE_PACKET, ContainerClosePacket::class);
		$this->registerPacket(ProtocolInfo::CONTAINER_OPEN_PACKET, ContainerOpenPacket::class);
		$this->registerPacket(ProtocolInfo::CONTAINER_SET_CONTENT_PACKET, ContainerSetContentPacket::class);
		$this->registerPacket(ProtocolInfo::CONTAINER_SET_DATA_PACKET, ContainerSetDataPacket::class);
		$this->registerPacket(ProtocolInfo::CONTAINER_SET_SLOT_PACKET, ContainerSetSlotPacket::class);
		$this->registerPacket(ProtocolInfo::CRAFTING_DATA_PACKET, CraftingDataPacket::class);
		$this->registerPacket(ProtocolInfo::CRAFTING_EVENT_PACKET, CraftingEventPacket::class);
		$this->registerPacket(ProtocolInfo::DISCONNECT_PACKET, DisconnectPacket::class);
		$this->registerPacket(ProtocolInfo::DROP_ITEM_PACKET, DropItemPacket::class);
		$this->registerPacket(ProtocolInfo::ENTITY_EVENT_PACKET, EntityEventPacket::class);
		$this->registerPacket(ProtocolInfo::EXPLODE_PACKET, ExplodePacket::class);
		$this->registerPacket(ProtocolInfo::FULL_CHUNK_DATA_PACKET, FullChunkDataPacket::class);
		$this->registerPacket(ProtocolInfo::HURT_ARMOR_PACKET, HurtArmorPacket::class);
		$this->registerPacket(ProtocolInfo::INTERACT_PACKET, InteractPacket::class);
		$this->registerPacket(ProtocolInfo::INVENTORY_ACTION_PACKET, InventoryActionPacket::class);
		$this->registerPacket(ProtocolInfo::ITEM_FRAME_DROP_ITEM_PACKET, ItemFrameDropItemPacket::class);
		$this->registerPacket(ProtocolInfo::LEVEL_EVENT_PACKET, LevelEventPacket::class);
		$this->registerPacket(ProtocolInfo::LEVEL_SOUND_EVENT_PACKET, LevelSoundEventPacket::class);
		$this->registerPacket(ProtocolInfo::LOGIN_PACKET, LoginPacket::class);
		$this->registerPacket(ProtocolInfo::MAP_INFO_REQUEST_PACKET, MapInfoRequestPacket::class);
		$this->registerPacket(ProtocolInfo::MOB_ARMOR_EQUIPMENT_PACKET, MobArmorEquipmentPacket::class);
		$this->registerPacket(ProtocolInfo::MOB_EQUIPMENT_PACKET, MobEquipmentPacket::class);
		$this->registerPacket(ProtocolInfo::MOVE_ENTITY_PACKET, MoveEntityPacket::class);
		$this->registerPacket(ProtocolInfo::MOVE_PLAYER_PACKET, MovePlayerPacket::class);
		$this->registerPacket(ProtocolInfo::PLAYER_FALL_PACKET, PlayerFallPacket::class);
		$this->registerPacket(ProtocolInfo::PLAYER_ACTION_PACKET, PlayerActionPacket::class);
		$this->registerPacket(ProtocolInfo::PLAYER_INPUT_PACKET, PlayerInputPacket::class);
		$this->registerPacket(ProtocolInfo::PLAYER_LIST_PACKET, PlayerListPacket::class);
		$this->registerPacket(ProtocolInfo::PLAY_STATUS_PACKET, PlayStatusPacket::class);
		$this->registerPacket(ProtocolInfo::REMOVE_BLOCK_PACKET, RemoveBlockPacket::class);
		$this->registerPacket(ProtocolInfo::REMOVE_ENTITY_PACKET, RemoveEntityPacket::class);
		$this->registerPacket(ProtocolInfo::REPLACE_ITEM_IN_SLOT_PACKET, ReplaceItemInSlotPacket::class);
		$this->registerPacket(ProtocolInfo::REQUEST_CHUNK_RADIUS_PACKET, RequestChunkRadiusPacket::class);
		$this->registerPacket(ProtocolInfo::RESOURCE_PACK_CHUNK_REQUEST_PACKET, ResourcePackChunkRequestPacket::class);
		$this->registerPacket(ProtocolInfo::RESOURCE_PACK_CHUNK_DATA_PACKET, ResourcePackChunkDataPacket::class);
		$this->registerPacket(ProtocolInfo::RESOURCE_PACK_CLIENT_RESPONSE_PACKET, ResourcePackClientResponsePacket::class);
		$this->registerPacket(ProtocolInfo::RESOURCE_PACK_DATA_INFO_PACKET, ResourcePackDataInfoPacket::class);
		$this->registerPacket(ProtocolInfo::RESOURCE_PACKS_INFO_PACKET, ResourcePacksInfoPacket::class);
		$this->registerPacket(ProtocolInfo::RESOURCE_PACK_STACK_PACKET, ResourcePackStackPacket::class);
		$this->registerPacket(ProtocolInfo::RESPAWN_PACKET, RespawnPacket::class);
		$this->registerPacket(ProtocolInfo::RIDER_JUMP_PACKET, RiderJumpPacket::class);
		$this->registerPacket(ProtocolInfo::SHOW_CREDITS_PACKET, ShowCreditsPacket::class);
		$this->registerPacket(ProtocolInfo::SERVER_TO_CLIENT_HANDSHAKE_PACKET, ServerToClientHandshakePacket::class);
		$this->registerPacket(ProtocolInfo::SET_COMMANDS_ENABLED_PACKET, SetCommandsEnabledPacket::class);
		$this->registerPacket(ProtocolInfo::SET_DIFFICULTY_PACKET, SetDifficultyPacket::class);
		$this->registerPacket(ProtocolInfo::SET_ENTITY_DATA_PACKET, SetEntityDataPacket::class);
		$this->registerPacket(ProtocolInfo::SET_ENTITY_LINK_PACKET, SetEntityLinkPacket::class);
		$this->registerPacket(ProtocolInfo::SET_ENTITY_MOTION_PACKET, SetEntityMotionPacket::class);
		$this->registerPacket(ProtocolInfo::SET_HEALTH_PACKET, SetHealthPacket::class);
		$this->registerPacket(ProtocolInfo::SET_PLAYER_GAME_TYPE_PACKET, SetPlayerGameTypePacket::class);
		$this->registerPacket(ProtocolInfo::SET_SPAWN_POSITION_PACKET, SetSpawnPositionPacket::class);
		$this->registerPacket(ProtocolInfo::SET_TIME_PACKET, SetTimePacket::class);
		$this->registerPacket(ProtocolInfo::SPAWN_EXPERIENCE_ORB_PACKET, SpawnExperienceOrbPacket::class);
		$this->registerPacket(ProtocolInfo::START_GAME_PACKET, StartGamePacket::class);
		$this->registerPacket(ProtocolInfo::TAKE_ITEM_ENTITY_PACKET, TakeItemEntityPacket::class);
		$this->registerPacket(ProtocolInfo::TEXT_PACKET, TextPacket::class);
		$this->registerPacket(ProtocolInfo::TRANSFER_PACKET, TransferPacket::class);
		$this->registerPacket(ProtocolInfo::UPDATE_BLOCK_PACKET, UpdateBlockPacket::class);
		$this->registerPacket(ProtocolInfo::UPDATE_TRADE_PACKET, UpdateTradePacket::class);
		$this->registerPacket(ProtocolInfo::USE_ITEM_PACKET, UseItemPacket::class);
		$this->registerPacket(ProtocolInfo::BLOCK_PICK_REQUEST_PACKET, BlockPickRequestPacket::class);
		$this->registerPacket(ProtocolInfo::COMMAND_BLOCK_UPDATE_PACKET, CommandBlockUpdatePacket::class);
		$this->registerPacket(ProtocolInfo::PLAY_SOUND_PACKET, PlaySoundPacket::class);
		$this->registerPacket(ProtocolInfo::SET_TITLE_PACKET, SetTitlePacket::class);
		$this->registerPacket(ProtocolInfo::STOP_SOUND_PACKET, StopSoundPacket::class);
		//TODO Minecraft: PE version 1.0.0 to 1.0.4 Packet
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::ADD_ENTITY_PACKET, \pocketmine\network\protocol\p102\AddEntityPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::ADD_HANGING_ENTITY_PACKET, \pocketmine\network\protocol\p102\AddHangingEntityPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::ADD_ITEM_ENTITY_PACKET, \pocketmine\network\protocol\p102\AddItemEntityPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::ADD_ITEM_PACKET, \pocketmine\network\protocol\p102\AddItemPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::ADD_PAINTING_PACKET, \pocketmine\network\protocol\p102\AddPaintingPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::ADD_PLAYER_PACKET, \pocketmine\network\protocol\p102\AddPlayerPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::ADVENTURE_SETTINGS_PACKET, \pocketmine\network\protocol\p102\AdventureSettingsPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::ANIMATE_PACKET, \pocketmine\network\protocol\p102\AnimatePacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::AVAILABLE_COMMANDS_PACKET, \pocketmine\network\protocol\p102\AvailableCommandsPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::BATCH_PACKET, BatchPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::BLOCK_ENTITY_DATA_PACKET, \pocketmine\network\protocol\p102\BlockEntityDataPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::BLOCK_EVENT_PACKET, \pocketmine\network\protocol\p102\BlockEventPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::CHANGE_DIMENSION_PACKET, \pocketmine\network\protocol\p102\ChangeDimensionPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::CHUNK_RADIUS_UPDATED_PACKET, \pocketmine\network\protocol\p102\ChunkRadiusUpdatedPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::CLIENTBOUND_MAP_ITEM_DATA_PACKET, \pocketmine\network\protocol\p102\ClientboundMapItemDataPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::COMMAND_STEP_PACKET, \pocketmine\network\protocol\p102\CommandStepPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::CONTAINER_CLOSE_PACKET, \pocketmine\network\protocol\p102\ContainerClosePacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::CONTAINER_OPEN_PACKET, \pocketmine\network\protocol\p102\ContainerOpenPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::CONTAINER_SET_CONTENT_PACKET, \pocketmine\network\protocol\p102\ContainerSetContentPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::CONTAINER_SET_DATA_PACKET, \pocketmine\network\protocol\p102\ContainerSetDataPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::CONTAINER_SET_SLOT_PACKET, \pocketmine\network\protocol\p102\ContainerSetSlotPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::CRAFTING_DATA_PACKET, \pocketmine\network\protocol\p102\CraftingDataPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::CRAFTING_EVENT_PACKET, \pocketmine\network\protocol\p102\CraftingEventPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::DISCONNECT_PACKET, \pocketmine\network\protocol\p102\DisconnectPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::DROP_ITEM_PACKET, \pocketmine\network\protocol\p102\DropItemPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::ENTITY_EVENT_PACKET, \pocketmine\network\protocol\p102\EntityEventPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::EXPLODE_PACKET, \pocketmine\network\protocol\p102\ExplodePacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::FULL_CHUNK_DATA_PACKET, \pocketmine\network\protocol\p102\FullChunkDataPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::HURT_ARMOR_PACKET, \pocketmine\network\protocol\p102\HurtArmorPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::INTERACT_PACKET, \pocketmine\network\protocol\p102\InteractPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::INVENTORY_ACTION_PACKET, \pocketmine\network\protocol\p102\InventoryActionPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::ITEM_FRAME_DROP_ITEM_PACKET, \pocketmine\network\protocol\p102\ItemFrameDropItemPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::LOGIN_PACKET, \pocketmine\network\protocol\p102\LoginPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::MAP_INFO_REQUEST_PACKET, \pocketmine\network\protocol\p102\MapInfoRequestPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::MOB_ARMOR_EQUIPMENT_PACKET, \pocketmine\network\protocol\p102\MobArmorEquipmentPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::MOB_EQUIPMENT_PACKET, \pocketmine\network\protocol\p102\MobEquipmentPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::MOVE_ENTITY_PACKET, \pocketmine\network\protocol\p102\MoveEntityPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::MOVE_PLAYER_PACKET, \pocketmine\network\protocol\p102\MovePlayerPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::PLAYER_ACTION_PACKET, \pocketmine\network\protocol\p102\PlayerActionPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::PLAYER_FALL_PACKET, \pocketmine\network\protocol\p102\PlayerFallPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::PLAYER_INPUT_PACKET, \pocketmine\network\protocol\p102\PlayerInputPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::PLAYER_LIST_PACKET, \pocketmine\network\protocol\p102\PlayerListPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::PLAY_STATUS_PACKET, \pocketmine\network\protocol\p102\PlayerStatusPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::REMOVE_BLOCK_PACKET, \pocketmine\network\protocol\p102\RemoveBlockPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::REMOVE_ENTITY_PACKET, \pocketmine\network\protocol\p102\RemoveEntityPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::REPLACE_ITEM_IN_SLOT_PACKET, \pocketmine\network\protocol\p102\ReplaceItemInSlotPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::REQUEST_CHUNK_RADIUS_PACKET, \pocketmine\network\protocol\p102\RequestChunkRadiusPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::RESOURCE_PACK_CLIENT_RESPONSE_PACKET, \pocketmine\network\protocol\p102\ResourcePackClientResponsePacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::RESOURCE_PACKS_INFO_PACKET, \pocketmine\network\protocol\p102\ResourcePacksInfoPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::RESPAWN_PACKET, \pocketmine\network\protocol\p102\RespawnPacket::class);
		//TODO
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::SET_COMMANDS_ENABLED_PACKET, \pocketmine\network\protocol\p102\SetCommandsEnabledPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::SET_DIFFICULTY_PACKET, \pocketmine\network\protocol\p102\SetDifficultyPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::SET_ENTITY_DATA_PACKET, \pocketmine\network\protocol\p102\SetEntityDataPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::SET_ENTITY_LINK_PACKET, \pocketmine\network\protocol\p102\SetEntityLinkPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::SET_ENTITY_MOTION_PACKET, \pocketmine\network\protocol\p102\SetEntityMotionPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::SET_HEALTH_PACKET, \pocketmine\network\protocol\p102\SetHealthPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::SET_PLAYER_GAME_TYPE_PACKET, \pocketmine\network\protocol\p102\SetPlayerGameTypePacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::SET_SPAWN_POSITION_PACKET, \pocketmine\network\protocol\p102\SetSpawnPositionPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::SET_TIME_PACKET, \pocketmine\network\protocol\p102\SetTimePacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::SPAWN_EXPERIENCE_ORB_PACKET, \pocketmine\network\protocol\p102\SpawnExperienceOrbPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::START_GAME_PACKET, \pocketmine\network\protocol\p102\StartGamePacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::TAKE_ITEM_ENTITY_PACKET, \pocketmine\network\protocol\p102\TakeItemEntityPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::TEXT_PACKET, \pocketmine\network\protocol\p102\TextPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::TRANSFER_PACKET, \pocketmine\network\protocol\p102\TransferPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::UPDATE_BLOCK_PACKET, \pocketmine\network\protocol\p102\UpdateBlockPacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::UPDATE_TRADE_PACKET, \pocketmine\network\protocol\p102\UpdateTradePacket::class);
		$this->registerPacket(\pocketmine\network\protocol\p102\Info::USE_ITEM_PACKET, \pocketmine\network\protocol\p102\UseItemPacket::class);
	}
}
