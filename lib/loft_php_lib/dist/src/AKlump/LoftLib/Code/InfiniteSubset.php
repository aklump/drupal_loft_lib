<?php


namespace AKlump\LoftLib\Code;


use AKlump\Data\Data;

/**
 * Class InfiniteSubset
 *
 * Return randomly ordered slices of a dataset.  Designed to work with the session for persistence of state across page
 * loads. An example use case is to show three different tiles at the bottom of each page, which change each page load,
 * and are pulled from a larger set of tiles.  When all tiles in the superset are shown, show them again, but this time
 * in a different order, never running out of sets.
 *
 * If $_SESSION is not the desired way to hold state, then you may pass the third argument to the constructor, a
 * pass-by-reference array which will be used to hold state instead of $_SESSION.
 *
 * @code
 *  // Each time this page loads, 3 tile nids will be loaded from the list of nids.
 *  $nids = new InfiniteSubset([255, 365, 987, 123, 455, 99, 101, 345], 'round_robin.related.123');
 *  $tiles = node_]oad_multiple($nids->slice(3));
 * @endcode
 *
 * @package AKlump\LoftLib\Code
 */
class InfiniteSubset {

    protected $g;

    protected $stateArray;

    protected $stateArrayPath;

    /**
     * InfiniteSubset constructor.
     *
     * @param string            $stateArrayPath The dot separated path in $stateArray.
     * @param  array            $dataset        The original array to step through.  Keys must not
     *                                          be important as only the values will be used.
     *                                          Elements should be single values (strings, int, etc)
     *                                          not arrays nor objects.
     * @param array             $stateArray     Defaults to $_SESSION.  An array to hold state.
     * @param \AKlump\Data\Data $data           Only needed to override default.
     */
    public function __construct($stateArrayPath = '', $dataset = array(), array &$stateArray = null, Data $data = null)
    {
        $this->g = $data ? $data : new Data();
        if ($stateArray === null) {
            $this->container =& $_SESSION;
        }
        else {
            $this->container =& $stateArray;
        }
        $this->containerPath = $stateArrayPath;
        $this->reset($dataset);
    }

    /**
     * Return a randomly ordered slice of dataset $count items long.
     *
     * @param int $count
     *
     * @return array
     */
    public function slice($count)
    {
        if (!($this->getDataset())) {
            throw new \RuntimeException("Dataset may not be empty for slice().");
        }
        $stack = $this->getStack();
        if (!is_array($stack)) {
            throw new \RuntimeException("Stack must be an array");
        }
        while (count($stack) < $count) {
            $stack = array_merge($stack, $this->getSortedDataset());
        }
        $slice = array_slice($stack, 0, $count, true);
        $stack = array_slice($stack, $count, null, true);
        $this->setContainerData($stack);

        return $slice;
    }

    /**
     * Return the original dataset, order untouched.
     *
     * @return array
     */
    public function getDataset()
    {
        return $this->getContainerData()['dataset'];
    }

    public function reset(array $dataset)
    {
        return $this->setContainerData(null, $dataset)
                    ->setContainerData($this->getSortedDataset());
    }

    /**
     * Return the current stack, randomized order, less any values already sliced.
     *
     * @return mixed
     */
    private function getStack()
    {
        return $this->getContainerData()['stack'];
    }

    /**
     * Return the dataset in a new random order.
     *
     * You may want to extend this class and override this method to control sorting algorithm.
     *
     * @return array
     */
    private function getSortedDataset()
    {
        return Arrays::shuffleWithKeys($this->getDataset());
    }

    /**
     * Return the container data.
     *
     * @return mixed
     */
    private function getContainerData()
    {
        $default = [
            'stack' => [],
            'dataset' => [],
        ];
        if (!$this->containerPath) {
            return $this->container + $default;
        }
        else {
            return $this->g->get($this->container, $this->containerPath, $default, function ($value, $default) {
                return $value + $default;
            });
        }
    }

    /**
     * Sets the data into our container.
     *
     * @param $stack
     *
     * @return $this
     */
    private function setContainerData(array $stack = null, array $dataset = null)
    {
        $value = $this->getContainerData();
        if (!is_null($stack)) {
            $value['stack'] = $stack;
        }
        if (!is_null($dataset)) {
            $value['dataset'] = $dataset;
        }
        if (!$this->containerPath) {
            $this->container = $value;
        }
        else {
            $this->g->set($this->container, $this->containerPath, $value);
        }

        return $this;
    }
}
