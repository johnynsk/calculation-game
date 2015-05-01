<?php
class Game_Card
{
	/**
	 * Иконки мастей
	 *
	 * @var string[]
	 */
	protected $_suitsIcons = ['♠', '♣', '♥', '♦']; 


	/**
	 * Иконки карт
	 *
	 * @var string[]
	 */
	protected $_cardsIcons = ['Т', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'В', 'Д', 'К'];


	/**
	 * Масти
	 *
	 * @var string[]
	 */
	protected $_suits = ['spades', 'clubs', 'worms', 'diamonds'];


	/**
	 * Номиналы
	 *
	 * @var string[]
	 */
	protected $_cards = ['ace', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'jack', 'queen', 'king'];


	/**
	 * Масть
	 *
	 * @var int
	 */
	protected $_suit;

	/**
	 * Номинал
	 * 
	 * @var int
	 */
	protected $_nominal;


	/**
	 * Универсальный getter
	 *
	 * @param string $name
	 */
	public function __get($name)
	{
		switch($name) {
			case 'nominalValue':
				return $this->_cards[$this->_nominal];
			case 'nominal':
				return $this->_nominal;
			case 'nominalIcon':
				return $this->_cardsIcons[$this->_nominal];
			case 'suitValue':
				return $this->_suits[$this->_suit];
			case 'suit':
				return $this->_suit;
			case 'suitIcon':
				return $this->_suitsIcons[$this->_suit];
		}		
	}


	/**
	 * @param int $suit
	 * @param int $nominal
	 */
	public function __construct($suit, $nominal)
	{
		$this->_nominal = $nominal;
		$this->_suit = $suit;
	}


	/**
	 * Распечатать значение
	 */
	public function dump()
	{
		return $this->suitIcon.$this->nominalIcon;
	}
}
