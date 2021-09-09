<?php

declare(strict_types = 1);

namespace HighestDreams\CustomNPC\Form\formapi;

use pocketmine\Player;

class ModalForm extends Form {
	
	private $content;
	
	private $labelMap = [];

    /**
     * @param callable $callable
     */
	public function __construct(?callable $callable) {
		parent::__construct($callable);
		$this->data["type"] = "modal";
		$this->data["title"] = "";
		$this->data["content"] = $this->content;
    	$this->data["button1"] = "";
    	$this->data["button2"] = "";
	}
	
	/**
	 * @param Player $player
	 */
	public function sendToPlayer(Player $player) : void {
		$player->sendForm($this);
	}
	/**
	 * @param string $title
	 */
	public function setTitle(string $title) : void {
		$this->data["title"] = $title;
	}
	/**
	 * @return string
	 */
	public function getTitle() : string {
		return $this->data["title"];
	}
	/**
	 * @return string
	 */
	public function getContent() : string {
		return $this->data["content"];
	}
	/**
	 * @param string $content
	 */
	public function setContent(string $content) : void {
		$this->data["content"] = $content;
	}
  
	/**
	 * @param string $text
	 */
	public function setButton1(string $text) : void {
		$this->data["button1"] = $text;
	}
  
	/**
	 * @return string
	 */
	public function getButton1() : string {
		return $this->data["button1"];
	}
  
	/**
	 * @param string $text
	 */
	public function setButton2(string $text) : void {
		$this->data["button2"] = $text;
	}
  
	/**
	 * @return string
	 */
	public function getButton2() : string {
		return $this->data["button2"];
	}
}
