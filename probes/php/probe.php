 <?php
/* NOTICE: The extra space at the beginning of the file is here on purpose and
 *         is necessary on PHP 7.2 (?) to prevent the first bracket from being
 *         stripped when the file is interpreted by the PHP processor after
 *         piping through SSH (exact cause still unknown, maybe a wrong BOM
 *         processing by PHP).
 */

//** @see https://stackoverflow.com/a/43056278 */
ini_set('serialize_precision', -1);

class Probe
{
    /**@var string */
    protected
        $host,
        $env,
        $probe;

    public function __construct() {
        $this->host = getenv('PROBE_HOSTNAME');
        $this->env = getenv('PROBE_ENV');
        $this->probe = getenv('PROBE_NAME');
    }

    /**
     * @return string
     */
    public function getConfig()
    {
        return file_get_contents('php://stdin') ?: '';
    }

    public function sendResults($results, array $additionalLabels = array()) {
        echo json_encode(array(
            'labels' => array_merge(
                array(
                    'hostname' => $this->host,
                    'env' => $this->env,
                    'probe' => $this->probe,
                    'probe_args' => '',
                ),
                $additionalLabels
            ),
            'results' => $results
        ));
    }
}
?>
