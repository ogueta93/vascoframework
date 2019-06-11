<?php
namespace Core\Helpers;

/**
 * DataTransform class
 **/
class DataTransform
{
    /* Object Properties */
    protected $originalData = null;
    protected $originalType = null;

    /* Object Constans */
    const ARRAY_TYPE = 'array';

    /**
     * Default Constructor
     *
     * @param $originalData
     **/
    public function __construct($originalData)
    {
        $this->originalData = $originalData;
        $this->originalType = gettype($originalData);
    }

    /**
     * Get original data type
     *
     * @return $originalType
     **/
    public function getOriginalType()
    {
        return $this->originalType;
    }

    /**
     * Transform originalData to stdClass format
     *
     * @return $data stdClass
     **/
    public function toStdClass()
    {
        $data = null;

        $caca = json_encode($this->originalData);
        switch ($this->originalType) {
            case self::ARRAY_TYPE:
                $data = json_decode(json_encode($this->originalData));
                if (count((array) $data) == 0) {
                    $data = new \stdClass();
                }
                break;
            default:
                $data = $this->originalData;
                break;
        }

        return $data;
    }

    /**
     * Transform originalData to JSON format
     *
     * @return $data JSON
     **/
    public function toJSON()
    {
        return json_encode($this->originalData);
    }
}
