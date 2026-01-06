<?php
namespace Vendor\DiscountLogger\Console\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class ShowDiscountLogCommand extends Command
{

    /**
     * @param Filesystem $filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $logFileName;
    /**
     * @param Filesystem $filesystem
     * @param string $logFileName <-- Inyectado desde di.xml
    */
    public function __construct (
	Filesystem $filesystem, 
	string $logFileName) 
     {
        $this->filesystem = $filesystem;
        $this->logFileName = $logFileName;
        parent::__construct();
    }

    protected function configure() {
	$this->setName('discountlog:tail')
	     ->setDescription('Muestra las ultimas 5 entradas de log de descuentos');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
	try {
            // Se usa la propiedad inyectada en lugar del texto "quemado"
            $fileName = $this->logFileName;
            $logDirectory = $this->filesystem->getDirectoryRead(DirectoryList::LOG);
            $filePath = $logDirectory->getAbsolutePath($fileName);
            if (!file_exists($filePath)) {
                $output->writeln("<error>El archivo no existe aun.</error>");
                return Command::SUCCESS;
            }
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (empty($lines)) {
                $output->writeln("<info>El archivo esta vacio.</info>");
                return Command::SUCCESS;
            }
            $lastLines = array_slice($lines, -5);
            $output->writeln("<info>Ultimas entradas de $fileName:</info>");
            foreach ($lastLines as $line) {
                $output->writeln($line);
            }
            return Command::SUCCESS;
        }catch (\Exception $e) {
            $output->writeln("<error>Error: " . $e->getMessage() . "</error>"); 
            return Command::FAILURE;
        }
    }
}
