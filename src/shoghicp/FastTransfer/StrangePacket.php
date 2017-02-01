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

use pocketmine\network\protocol\TransferPacket;

/**
  * @deprecated Use pocketmine\network\protocol\TransferPacket!
  */
class StrangePacket extends TransferPacket{
	
	public function __construct(){
		parent::__construct();
		$this->port = 19132; //Compatibilty for not needing to set port.
	}
}