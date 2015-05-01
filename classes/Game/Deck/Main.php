<?php
class Game_Deck_Main extends Game_Deck_Abstract
{
	/**
	 * Лимит отображаемых карт
	 */
	protected $_limitCards = 1;


	/**
	 * Заполнить карты в случайном порядке
	 *
	 * @return $this
	 */
	public function fill()
	{
		while(count($this->_keys) < Game_Decks::MAX_DECK_CARDS) {
			$suit = rand() % 4;
			$nominal = rand() % 13;

			if ($this->search($suit, $nominal) || ($suit < 4 && $suit == $nominal)) {
				continue;
			}

			$card = new Game_Card($suit, $nominal);
			$this->push($card);
		}

		return $this;
	}
}
