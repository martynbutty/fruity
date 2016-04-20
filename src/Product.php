<?php

namespace fruity;

class Product
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var float
     */
    private $unitPrice = 0.0;

    /**
     * @var string
     */
    private $desc;

    /**
     * @var float
     */
    private $pageSize = 0.0;

    /**
     * @var string
     */
    private $link;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return float
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @param float $unitPrice
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     * @return string
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @param string $desc
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    }

    /**
     * @return float
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @param float $pageSize
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    public function getBytesReadInkb()
    {
        if ($this->pageSize > 0) {
            return round(($this->pageSize / 1024), 0) . 'kb';
        } else {
            return 0;
        }
    }

    public function findDescriptionText()
    {
        $htmlReader = new Reader($this->getLink());
        $htmlReader->readFile();

        $parser = new Parser($htmlReader->getContent());
        $desc = $parser->lookForDescription();

        $this->desc = $desc;
        $this->pageSize = $htmlReader->getBytesRead();
    }

}