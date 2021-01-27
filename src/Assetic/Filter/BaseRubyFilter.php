<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

abstract class BaseRubyFilter extends BaseProcessFilter
{
    private $gemHome = '';
    private $gemPaths = array();

    /**
     * @return string
     */
    public function getGemHome()
    {
        return $this->gemHome;
    }

    /**
     * @param string $gemHome
     */
    public function setGemHome($gemHome)
    {
        $this->gemHome = $gemHome;
    }

    /**
     * @return array
     */
    public function getGemPaths()
    {
        return $this->gemPaths;
    }

    /**
     * @param array $gemPaths
     */
    public function setGemPaths(array $gemPaths)
    {
        $this->gemPaths = $gemPaths;
    }

    /**
     * @param string $gemPath
     */
    public function addGemPath($gemPath)
    {
        $this->gemPaths[] = $gemPath;
    }

    protected function createProcessBuilder(array $arguments = array())
    {
        $pb = parent::createProcessBuilder($arguments);

        $this->mergeEnv($pb);

        if ($this->gemHome) {
            $pb->setEnv('GEM_HOME', $this->gemHome);
        }

        if ($this->gemPaths) {
            $pb->setEnv('GEM_PATH', implode(PATH_SEPARATOR, $this->gemPaths));
        }

        return $pb;
    }
}
