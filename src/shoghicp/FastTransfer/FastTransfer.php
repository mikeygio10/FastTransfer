<?php

/*
 * FastTransfer plugin for PocketMine-MP
 * Copyright (C) 2015 Shoghi Cervantes <https://github.com/shoghicp/FastTransfer>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

namespace shoghicp\FastTransfer;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\TranslationContainer;
use pocketmine\network\Network;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class FastTransfer extends PluginBase{

	/**
	 * Will transfer a connected player to another server.
	 * This will trigger PlayerTransferEvent
	 *
	 * Player transfer might not be instant if you use a DNS address instead of an IP address
	 *
	 * @param Player $player
	 * @param string $address
	 * @param int    $port
	 * @param string $message If null, ignore message
	 *
	 * @return bool
	 */
	public function transferPlayer(Player $player, string $address, int $port = 19132, string $message = "You are being transferred"): bool{
		$ev = new PlayerTransferEvent($player, $address, $port, $message);
		$ev->call();
		if($ev->isCancelled()){
			return false;
		}

		$ip = $ev->getAddress();

		if($ip === null){
			return false;
		}
		
		if($message !== null and $message !== ""){
			$player->sendMessage($message);	
		}

		$packet = new StrangePacket();
		$packet->address = $ip;
		$packet->port = $ev->getPort();
		$player->dataPacket($packet);

		return true;
	}

	/**
	 * Clear the DNS lookup cache.
	 */
	public function cleanLookupCache(){
		$this->lookup = [];
	}


	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
		if($label === "transfer"){
			if(count($args) < 2 or count($args) > 3 or (count($args) === 2 and !($sender instanceof Player))){
				$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$command->getUsage()]));

				return true;
			}

			/** @var Player $target */
			$target = $sender;

			if(count($args) === 3){
				$target = $sender->getServer()->getPlayer($args[0]);
				$address = $args[1];
				$port = (int) $args[2];
			}else{
				$address = $args[0];
				$port = (int) $args[1];
			}

			if($target === null){
				$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.player.notFound"));
				return true;
			}

			$sender->sendMessage("Transferring player " . $target->getDisplayName());
			if(!$this->transferPlayer($target, $address, $port)){
				$sender->sendMessage(TextFormat::RED . "An error occurred during the transfer");
			}

			return true;
		}

		return false;
	}
}
