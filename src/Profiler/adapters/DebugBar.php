<?

namespace Nip\Profiler\Adapters;

use Nip\DebugBar\DataCollector\QueryCollector;
use Nip\Profiler\Profile;

class DebugBar extends AbstractAdapter
{

    /**
     * @var QueryCollector
     */
    protected $collector = null;

    public function write(Profile $profile)
    {
        $this->getCollector()->addQuery($profile);
    }


    /**
     * @return QueryCollector
     */
    public function getCollector()
    {
        return $this->collector;
    }

    /**
     * @param QueryCollector $colector
     */
    public function setCollector(QueryCollector $colector)
    {
        $this->collector = $colector;
    }

}