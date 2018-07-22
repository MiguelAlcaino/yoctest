<?php
/**
 * Created by PhpStorm.
 * User: malcaino
 * Date: 20/07/18
 * Time: 15:44
 */

namespace App\Command;


use App\Entity\City;
use App\Entity\Country;
use App\Entity\WeatherRecordDaily;
use App\Exceptions\CityNotFoundException;
use App\Exceptions\CountryNotFoundException;
use App\Services\WeatherApiService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RequestReportingDataCommand extends Command
{

    private $doctrine;

    private $weatherApiService;

    public function __construct(ManagerRegistry $doctrine, WeatherApiService $weatherApiService)
    {
        $this->doctrine = $doctrine;
        $this->weatherApiService = $weatherApiService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('yoc:weather:request')
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user...')
            ->addOption('country-code',null, InputArgument::OPTIONAL,' Country code',null)
            ->addOption('city-name',null,InputArgument::OPTIONAL,'City name', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        try{
            $result = $this->weatherApiService->getCityWeatherReport($input->getOption('country-code'), $input->getOption('city-name'));
            $this->processResult($result, $output);

            $io->success('The request has been completed successfully');
        }catch (CountryNotFoundException $exception){
            $io->error('Country Code not found. Please use a valid country-code');
        }catch (CityNotFoundException $exception){
            $io->error('City name not found. Please use a valid city-name');
        }
    }

    /**
     * Checks and persists new cities and weather records.
     * @param array $result
     * @param OutputInterface $output
     */
    private function processResult(array $result, OutputInterface $output){
        $manager = $this->doctrine->getManager();
        foreach($result as $weatherReport){
            $cityCheck = false;
            $city = null;
            foreach($weatherReport as $dailyRecord){
                if(!$cityCheck){
                    $city = $manager->getRepository(City::class)->findOneBy([
                        'name' => $dailyRecord->city_name
                    ]);

                    $cityCheck = true;

                    if(is_null($city)){

                        $country = $manager->getRepository(Country::class)->findOneBy([
                            'countryCode' => $dailyRecord->country_code
                        ]);

                        if(is_null($country)){
                            $country = (new Country())
                                ->setCountryCode($dailyRecord->country_code)
                                ->setName(WeatherApiService::ALLOWED_COUNTRY_CITIES[$dailyRecord->country_code]['name']);
                            $manager->persist($country);
                            $manager->flush();
                        }

                        $city = (new City())
                            ->setName($dailyRecord->city_name)
                            ->setCountry($country)
                            ->setTimezone($dailyRecord->timezone);
                        $manager->persist($city);
                        $manager->flush();
                    }
                }

                if(!is_null($dailyRecord)){
                    $datetime = new \DateTime($dailyRecord->data[0]->datetime);

                    $weatherRecordDaily = $manager->getRepository(WeatherRecordDaily::class)->findOneBy([
                        'city' => $city,
                        'datetime' => $datetime
                    ]);

                    if(is_null($weatherRecordDaily)){
                        $weatherRecordDaily = (new WeatherRecordDaily())
                            ->setDatetime($datetime)
                            ->setMaxTemp($dailyRecord->data[0]->max_temp)
                            ->setMinTemp($dailyRecord->data[0]->min_temp)
                            ->setTemp($dailyRecord->data[0]->temp)
                            ->setCity($city);
                        $output->writeln('Adding data for '.$city->getName().' '.$weatherRecordDaily->getDatetime()->format('Y-m-d'));
                        $manager->persist($weatherRecordDaily);
                        $manager->flush();
                    }else{
                        $output->writeln('<error>Entry is already inside the database. Avoiding persistance. '.$city->getName().' '.$weatherRecordDaily->getDatetime()->format('Y-m-d').'</error>');
                    }
                }else{
                    $output->writeln('<info>NULL Record found. Avoiding persistance</info>');
                }

            }
        }
    }


}