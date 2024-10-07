<?php

namespace App\Command;

use Twig\Environment;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(
    name: 'app:create-module',
    description: 'Create a new module for your project.',
)]
class GenerateModuleCommand extends Command
{
    private InputInterface $input;
    private OutputInterface $output;
    private HelperInterface $helper;
    private string $baseDir;
    private string $moduleName;
    private $allowedAttributeTypes = [
        'string',
        'text',
        'boolean',
        'float',
        'integer',
        'datetime_immutable',
        'datetime',
        'bigint',
        'smallint',
        'guid',
        'time',
        'date'
    ];

    //TODO: attention pour decimal il y a deux questions en plus  Scale (number of decimals to store: 100.00 would be 2) [0]: ET Can this field be null in the database (nullable) (yes/no) [no]:
    //TODO: relationships

    public function __construct(private Environment $twig)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            //->addArgument('hook', InputArgument::REQUIRED, 'Do you need a hook in your module?')
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }
    private function askForModuleName(): string
    {
        $helper = $this->getHelper('question');
        $question = new Question('What is your module name? (eg: Product): ');
        $moduleName = null;
    
        // Keep asking until the user provides a valid answer
        while (true) {
            $moduleName = $helper->ask($this->input, $this->output, $question);
    
            // Check if user didn't input anything
            if (empty($moduleName)) {
                $this->output->writeln('<error>Module name cannot be empty. Please try again.</error>');
                continue; // Restart the loop to ask again
            }
    
            // Ensure that the module name doesn't already end with "Module"
            if (preg_match('/module$/i', $moduleName)) {
                $moduleName = substr($moduleName, 0, -6); // Removes "module" (case-insensitive)
            }
    
            // Append "Module" to the module name
            $moduleName = ucfirst($moduleName) . 'Module';
            break; // Exit the loop when a valid input is given
        }
    
        return $moduleName;
    }
    

    private function askIfHookNeeded(): ?string
    {
        // Ask if the user needs a hook
        $question = new ConfirmationQuestion('Do you need a hook in the module? (yes/no): ', false);
        $needsHook = $this->helper->ask($this->input, $this->output, $question);
    
        if ($needsHook) {
            // Loop until a valid hook name is provided
            while (true) {
                $hookNameQuestion = new Question('What is your hook name? (eg: ProductDetail, ProductListing, etc): ');
                $hookName = $this->helper->ask($this->input, $this->output, $hookNameQuestion);
    
                // If hook name is empty, show an error and ask again
                if (empty($hookName)) {
                    $this->output->writeln('<error>Hook name cannot be empty. Please try again.</error>');
                    continue; // Restart the loop to ask again
                }
    
                // Ensures that the hook ends with "Hook" (case-insensitive)
                if (preg_match('/hook$/i', $hookName)) {
                    $hookName = substr($hookName, 0, -4); // Remove "Hook"
                }
    
                // Capitalize the first letter and append "Hook"
                $hookName = ucfirst($hookName) . 'Hook';
                break; // Exit the loop when a valid name is given
            }
    
            $this->output->writeln('Gotcha, I will create a hook with name: ' . $hookName);
            return $hookName;
        } else {
            $this->output->writeln('Gotcha, no hook required!');
            return null;
        }
    }
    
    private function createModule($moduleName): void
    {
        $this->baseDir = __DIR__ . '/../../src/Module/' . $moduleName;

        // Define the subdirectories you want to create
        $subDirectories = [
            $this->baseDir . '/Controller',
            $this->baseDir . '/Service',
            $this->baseDir . '/templates',
        ];
        // Create the base directory if it doesn't exist
        if (!is_dir($this->baseDir)) {
            mkdir($this->baseDir, 0755, true);
            $this->output->writeln("Created directory: " . $this->baseDir);
        }
        
        // Loop through each subdirectory and create it if it doesn't exist
        foreach ($subDirectories as $subDir) {
            if (!is_dir($subDir)) {
                mkdir($subDir, 0755, true);
                $this->output->writeln("Created directory: " . $subDir);
            }
        }

        //Create Controller file
        $this->generateModuleControllerAndTemplate($moduleName);
        $this->generateModuleDefaultService();
        $this->generateSiteMapService();

        //TODO: generate repository
        //TODO: add repository to SiteMapService
        //TODO: create hook
    }

    private function generateModuleDefaultService(){
        $defaultServiceFilePath = $this->baseDir . '/Service/' . substr($this->moduleName, 0, -6) . 'Service.php';
        if(!file_exists($defaultServiceFilePath)){
            /**
             * GENERATING THE Default Module Service
            */
            // Render the Twig template with the data for the PHP file
            $phpDefaultServiceContent = $this->twig->render('ExempleModule/Service/exemple_default_service.twig', [
                'moduleName' => $this->moduleName,
                'entityName' => substr($this->moduleName, 0, -6)
            ]);

            // Write the generated content to a file
            
            file_put_contents($defaultServiceFilePath, $phpDefaultServiceContent);
            $this->output->writeln("Generated default Service at: " . $defaultServiceFilePath);
        }
    }

    private function generateSiteMapService(){
        $siteMapServiceFilePath = $this->baseDir . '/Service/' . 'SiteMapService.php';
            if(!file_exists($siteMapServiceFilePath)){
            /**
             * GENERATING THE SiteMapService.php
            */
            $route = $this->convertToRouteUrl($this->moduleName); // Will output for exemple "product"

            // Render the Twig template with the data for the PHP file
            $phpSiteMapServiceContent = $this->twig->render('ExempleModule/Service/exemple_site_map_service.twig', [
                'route' => $route,
                'moduleName' => $this->moduleName
            ]);

            // Write the generated content to a file

            file_put_contents($siteMapServiceFilePath, $phpSiteMapServiceContent);
            $this->output->writeln("Generated SiteMap Service at: " . $siteMapServiceFilePath);
        }
    }

    private function generateModuleControllerAndTemplate(): void
    {
        /**
         * GENERATING THE CONTROLLER
        */
            $namespace = 'App\\Module\\' . $this->moduleName.'\\Controller';
            $route = $this->convertToRouteUrl($this->moduleName); // Will output for exemple "product"
            $className = substr($this->moduleName, 0, -6) . 'Controller'; // Will output for exemple "ProductController"
            $controllerFilePath = $this->baseDir . '/Controller/' . $className . '.php';

            if(!file_exists($controllerFilePath)){
                // Render the Twig template with the data for the PHP file
                $phpControllerContent = $this->twig->render('ExempleModule/Controller/exemple_controller.twig', [
                    'namespace' => $namespace,
                    'className' => $className,
                    'route' => $route,
                    'moduleName' => $this->moduleName
                ]);

                
                // Write the generated content to a file
                file_put_contents($controllerFilePath, $phpControllerContent);
                $this->output->writeln("Generated Controller at: " . $controllerFilePath);
            }
            
        /**
         * GENERATING THE index.html.twig FOR THE CONTROLLER
         */
            $templateFilePath = $this->baseDir . '/templates/' . 'index.html.twig';
            if(!file_exists($templateFilePath)){
                $indexTemplateContent = $this->twig->render('ExempleModule/templates/exemple_index.twig');
                // Write the generated content to a file
                file_put_contents($templateFilePath, $indexTemplateContent);              
                $this->output->writeln("Generated template at: " . $templateFilePath);
            }
    }

    /**
     * Will convert ProductTypeModule to product-type
    */
    private function convertToRouteUrl($input): string
    {
        // Step 1: Remove "Module" at the end if it exists
        if (substr($input, -6) === 'Module') {
            $input = substr($input, 0, -6);
        }
        // Step 2: Insert a hyphen before each uppercase letter (except the first one)
        $withHyphens = preg_replace('/([a-z])([A-Z])/', '$1-$2', $input);
            
        // Step 3: Convert the entire string to lowercase
        return strtolower($withHyphens);
    }

    private function askIfEntityNeeded(): string
    {
        // Ask if the user needs a entity
        $question = new ConfirmationQuestion('Do you need an Entity in the module? (yes/no): ', false);
        $needsEntity = $this->helper->ask($this->input, $this->output, $question);
    
        if ($needsEntity) {
            // Loop until a valid entity name is provided
            while (true) {
                $entityNameQuestion = new Question('What is your entity name? (eg: ProductDetail, ProductListing, etc): ');
                $entityName = $this->helper->ask($this->input, $this->output, $entityNameQuestion);
    
                // If entity name is empty, show an error and ask again
                if (empty($entityName)) {
                    $this->output->writeln('<error>Entity name cannot be empty. Please try again.</error>');
                    continue; // Restart the loop to ask again
                }
    
                // Ensures that the entity ends with "Entity" (case-insensitive)
                /*if (preg_match('/entity$/i', $entityName)) {
                    $entityName = substr($entityName, 0, -4); // Remove "Entity"
                }*/
    
                // Capitalize the first letter and append "Entity"
                $entityName = ucfirst($entityName);//. 'Entity';
                break; // Exit the loop when a valid name is given
            }
    
            $this->output->writeln("Gotcha, I will create a entity with name: " . $entityName."\n");
            return $entityName;
        } else {
            $this->output->writeln("Gotcha, no entity required!\n");
            return null;
        }
    }

    private function createEntity($entityName){
        $entitySubDirectory = $this->baseDir . '/Entity';

        // Create it if it doesn't exist
        if (!is_dir($entitySubDirectory)) {
            mkdir($entitySubDirectory, 0755, true);
            $this->output->writeln("Created directory: " . $entitySubDirectory);
        }

        if(!file_exists($entitySubDirectory.'/'.$entityName.'.php')){
            $this->generateEntity($entityName);
        }
        else{
            $this->output->writeln("Entity already exists, let's create new attributes\n");
        }
        $this->generateAttributes($entityName);
    }

    
    private function generateEntity($entityName): void
    {
        //$entityName = substr($entityName, 0, -6); // Removes "Entity",
        $question = new ConfirmationQuestion("Do you need to generate SEO meta attributes for your entity $entityName? (yes/no): ", false);
        $needsSeoMetaAttributes = $this->helper->ask($this->input, $this->output, $question);

        //TODO: Generate Repository
        $entityContent = $this->twig->render('ExempleModule/Entity/exemple_entity.twig', [
            'entityName' => $entityName,
            'metaSeo' => $needsSeoMetaAttributes,
            'moduleName' => $this->moduleName,
        ]);
        // Write the generated content to a file
        $entityFilePath = $this->baseDir . '/Entity/' . $entityName . '.php';
        file_put_contents($entityFilePath, $entityContent);

        //GENERATING ENTITY REPOSITORY
        $repositoryPath = $this->baseDir . '/Repository/' . $entityName . 'Repository.php';
        if(!file_exists($repositoryPath)){
            $repositoryContent = $this->twig->render('ExempleModule/Repository/exemple_repository.twig', [
                'entityName' => $entityName,
                'moduleName' => $this->moduleName,
            ]);
            $repositoryFolderPath = $this->baseDir.'/Repository';
            if (!is_dir($repositoryFolderPath)) {
                mkdir($this->baseDir.'/Repository', 0755, true);
            }
            // Write the generated content to a file
            file_put_contents($repositoryPath, $repositoryContent);
            $this->output->writeln("Generated Repository at: " . $repositoryPath);
        }
        /**
         * OUTPUTTING THE PATHS OF THE GENERATED CONTROLLER AND TEMPLATE
         */
        $this->output->writeln("Generated Entity at: " . $entityFilePath);
    }

    private function generateAttributes($entityName): void
    {
        $entityFilePath = $this->baseDir . '/Entity/' . $entityName . '.php';
        // Read the entity content
        $entityContent = file_get_contents($entityFilePath);
        // Search for the first occurrence of 'public function get'
        $getFunctionPos = strpos($entityContent, 'public function get');

        if($getFunctionPos !== false){
            $attributes = [];
            while(true){
                $attribute = $this->generateAttribute();
                if($attribute === null){
                    break;
                }
                array_push($attributes, $attribute);
                //$question = new ConfirmationQuestion('Do you want to generate another attribute? (yes/no): ', true);
                //$continueToAddAttributes = $this->helper->ask($this->input, $this->output, $question);
                /*if($continueToAddAttributes) {
                    continue;
                } else {
                    break;
                }*/
            }
            
            $attributesString = '';
            $attributesGettersSetters = '';

            foreach($attributes as $attribute){
                $attributeAndGettersSetters = $this->generateAttributeContent($attribute['attributeName'], $attribute['attributeType']);
                $attributesString .= $attributeAndGettersSetters['attributeContent'];
                $attributesGettersSetters .= $attributeAndGettersSetters['getterAndSetterContent'];
            }

            // Insert new attributes in the existing php file entity with proper indentation
            $entityContent = $this->insertWithIndentation($entityContent, $attributesString, $attributesGettersSetters, $getFunctionPos);

            // Write the modified content back to the file
            file_put_contents($entityFilePath, $entityContent);
        }
        else{
            $this->output->writeln("A getter is required to generate new attributes. Please add at least \"public function get\" below the entity attributes." . $entityFilePath);
            return;
        }
    }


    /**
     * Inserts the given attribute strings into the entity content with proper indentation.
     *
     * @param string $entityContent The original content of the entity file.
     * @param string $attributesString The string containing the new attributes to insert.
     * @param string $attributesGettersSetters The string containing the new getters/setters to insert.
     * @param int $getFunctionPos The position in the entity content where the insertion will occur.
     * @return string The modified entity content with the new attributes inserted.
     */
    private function insertWithIndentation(string $entityContent, string $attributesString, string $attributesGettersSetters, int $insertPos): string
    {
        // Get the indentation of the line containing 'public function get'
        $lines = explode("\n", $entityContent); // Split content into lines
        $lineIndex = substr_count(substr($entityContent, 0, $insertPos), "\n"); // Find the line index
        $indentation = '';

        // Check for indentation in the line
        if (isset($lines[$lineIndex])) {
            preg_match('/^(\s*)/', $lines[$lineIndex], $matches); // Match leading whitespace
            $indentation = $matches[1]; // Capture leading whitespace
        }

        // Prepend indentation to each line of the attributesGettersSetters
        $attributesGettersSetters = $indentation . str_replace("\n", "\n" . $indentation, $attributesGettersSetters);

        // Prepend indentation to the attributesString
        $attributesString = $indentation . str_replace("\n", "\n" . $indentation, $attributesString);

        // Insert the text before 'public function get'
        $entityContent = substr_replace($entityContent, $attributesGettersSetters, $insertPos, 0);
        $entityContent = substr_replace($entityContent, "\n" . $attributesString . "\n", $insertPos, 0);

        return $entityContent; // Return the modified content
    }

    private function generateAttributeContent($attributeName, $attributeType): array
    {
        $capitalizedAttributeName = ucfirst($attributeName);

        switch ($attributeType) {
            case 'text':
                $attributeContent = $this->twig->render('ExempleModule/Entity/components/bool/attribute.twig', [
                    'attributeName' => $attributeName
                ]);
                $getterAndSetterContent = $this->twig->render('ExempleModule/Entity/components/bool/gettersSetters.twig', [
                    'attributeName' => $attributeName,
                    'capitalizedattributeName' => $capitalizedAttributeName
                ]);
                break;
            case 'boolean':
                $attributeContent = $this->twig->render('ExempleModule/Entity/components/bool/attribute.twig', [
                    'attributeName' => $attributeName
                ]);
                $getterAndSetterContent = $this->twig->render('ExempleModule/Entity/components/bool/gettersSetters.twig', [
                    'attributeName' => $attributeName,
                    'capitalizedattributeName' => $capitalizedAttributeName
                ]);
                break;
            case 'float':
                $attributeContent = $this->twig->render('ExempleModule/Entity/components/float/attribute.twig', [
                    'attributeName' => $attributeName
                ]);
                $getterAndSetterContent = $this->twig->render('ExempleModule/Entity/components/float/gettersSetters.twig', [
                    'attributeName' => $attributeName,
                    'capitalizedattributeName' => $capitalizedAttributeName
                ]);
                break;
                break;
            case 'string':
                $attributeContent = $this->twig->render('ExempleModule/Entity/components/string/attribute.twig', [
                    'attributeName' => $attributeName
                ]);
                $getterAndSetterContent = $this->twig->render('ExempleModule/Entity/components/string/gettersSetters.twig', [
                    'attributeName' => $attributeName,
                    'capitalizedattributeName' => $capitalizedAttributeName
                ]);
                break;
            case 'integer':
                $attributeContent = $this->twig->render('ExempleModule/Entity/components/integer/attribute.twig', [
                    'attributeName' => $attributeName
                ]);
                $getterAndSetterContent = $this->twig->render('ExempleModule/Entity/components/integer/gettersSetters.twig', [
                    'attributeName' => $attributeName,
                    'capitalizedattributeName' => $capitalizedAttributeName
                ]);
                break;
            case 'smallint':
                $attributeContent = $this->twig->render('ExempleModule/Entity/components/smallint/attribute.twig', [
                    'attributeName' => $attributeName
                ]);
                $getterAndSetterContent = $this->twig->render('ExempleModule/Entity/components/smallint/gettersSetters.twig', [
                    'attributeName' => $attributeName,
                    'capitalizedattributeName' => $capitalizedAttributeName
                ]);
                break;
            case 'bigint':
                $attributeContent = $this->twig->render('ExempleModule/Entity/components/bigint/attribute.twig', [
                    'attributeName' => $attributeName
                ]);
                $getterAndSetterContent = $this->twig->render('ExempleModule/Entity/components/bigint/gettersSetters.twig', [
                    'attributeName' => $attributeName,
                    'capitalizedattributeName' => $capitalizedAttributeName
                ]);
                break;
            case 'datetime':
                $attributeContent = $this->twig->render('ExempleModule/Entity/components/datetime/attribute.twig', [
                    'attributeName' => $attributeName
                ]);
                $getterAndSetterContent = $this->twig->render('ExempleModule/Entity/components/datetime/gettersSetters.twig', [
                    'attributeName' => $attributeName,
                    'capitalizedattributeName' => $capitalizedAttributeName
                ]);
                break;
            case 'datetime_immutable':
                $attributeContent = $this->twig->render('ExempleModule/Entity/components/datetime_immutable/attribute.twig', [
                    'attributeName' => $attributeName
                ]);
                $getterAndSetterContent = $this->twig->render('ExempleModule/Entity/components/datetime_immutable/gettersSetters.twig', [
                    'attributeName' => $attributeName,
                    'capitalizedattributeName' => $capitalizedAttributeName
                ]);
                break;
            case 'date':
                $attributeContent = $this->twig->render('ExempleModule/Entity/components/date/attribute.twig', [
                    'attributeName' => $attributeName
                ]);
                $getterAndSetterContent = $this->twig->render('ExempleModule/Entity/components/date/gettersSetters.twig', [
                    'attributeName' => $attributeName,
                    'capitalizedattributeName' => $capitalizedAttributeName
                ]);
                break;
            case 'time':
                $attributeContent = $this->twig->render('ExempleModule/Entity/components/time/attribute.twig', [
                    'attributeName' => $attributeName
                ]);
                $getterAndSetterContent = $this->twig->render('ExempleModule/Entity/components/time/gettersSetters.twig', [
                    'attributeName' => $attributeName,
                    'capitalizedattributeName' => $capitalizedAttributeName
                ]);
                break;
            case 'guid':
                $attributeContent = $this->twig->render('ExempleModule/Entity/components/guid/attribute.twig', [
                    'attributeName' => $attributeName
                ]);
                $getterAndSetterContent = $this->twig->render('ExempleModule/Entity/components/guid/gettersSetters.twig', [
                    'attributeName' => $attributeName,
                    'capitalizedattributeName' => $capitalizedAttributeName
                ]);
                break;
        }

        return [
            'attributeContent' => $attributeContent . "\n",
            'getterAndSetterContent' => $getterAndSetterContent . "\n"
        ];
    }

    private function isValidAttributeType($attributeType): bool
    {
        // Check if the attribute type exists as a key in the allowedAttributeTypes array
        if (!in_array($attributeType, $this->allowedAttributeTypes)) {
            return false;
        }
        return true;
    }
    
    private function displayAllowedTypes(): void
    {
        $this->output->writeln('Allowed types are: ');//TODO:
    }

    private function generateAttribute(): ?array
    {
        // Loop until a valid entity name is provided
        while (true) {
            $attributeQuestion = new Question('What property do you want to add? (eg: "price float", "name string", etc): ');
            $attribute = $this->helper->ask($this->input, $this->output, $attributeQuestion);

            //The user wants to stop adding new attributes
            if(strtolower($attribute) === ''){
                return null;
            }

            //TODO: check if attribute already exists in the entity

            $words = explode(' ', $attribute);
            if (!isset($words[1])) 
            {
                $this->output->writeln('<error>Attribute type cannot be empty. Please try again.</error>');
                $this->displayAllowedTypes();
                continue; // Restart the loop to ask again
            }
            if(isset($words[1]) && !$this->isValidAttributeType(strtolower($words[1])))
            {
                $this->displayAllowedTypes();
                $this->output->writeln('<error>Attribute type is invalid. Please try again.</error>');
                continue; // Restart the loop to ask again
            }
            
            // If entity name is empty, show an error and ask again
            if (empty($words[0])) {
                $this->output->writeln('<error>Attribute name cannot be empty. Please try again.</error>');
                continue; // Restart the loop to ask again
            }

            // Capitalize the first letter and append "Entity"
            $attribute = lcfirst($words[0]);
            return ['attributeName' => $attribute, 'attributeType' => strtolower($words[1])];
        }
    }
    
    private function createHook($hookName): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;
        $this->helper = $this->getHelper('question');

        //TODO: check if module already exists, and if it does ask to create a hook directly
        $this->moduleName = $this->askForModuleName();
        //TODO: return if moduleName is empty
        $hookName = $this->askIfHookNeeded();
        $entityName = $this->askIfEntityNeeded();
        
        $this->createModule($this->moduleName);

        if($hookName !== null){
            $this->createHook($hookName);
        }

        if($entityName !== null){
            $this->createEntity($entityName);
        }

        $io = new SymfonyStyle($input, $output);
        
        $io->success('On a terminé!');

        return Command::SUCCESS;
    }
}
