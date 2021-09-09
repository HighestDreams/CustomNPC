<?php

declare(strict_types = 1);

namespace HighestDreams\CustomNPC\Form\formapi;

use pocketmine\Player;

class SimpleForm extends Form {

	const IMAGE_TYPE_PATH = 0;
	const IMAGE_TYPE_URL = 1;

	private $content = "";
	
	private $labelMap = [];

    /**
     * @param callable $callable
     */
	public function __construct(?callable $callable) {
		parent::__construct($callable);
		$this->data["type"] = "form";
		$this->data["title"] = "";
		$this->data["content"] = $this->content;
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
	 * @param int $imageType
	 * @param string $imagePath
	 */
	public function addButton(string $text, int $imageType = -1, string $imagePath = "") : void {
		$content = ["text" => $text];
		if($imageType !== -1){
			$content["image"]["type"] = $imageType === 0 ? "path" : "url";
			$content["image"]["data"] = $imagePath;
		}
		$this->data["buttons"][] = $content;
	}

}
