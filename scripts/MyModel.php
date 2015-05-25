<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-3-18
 * Time: 下午3:39
 */

use Phalcon\Db\Column;
use Phalcon\Text as Utils;
use Phalcon\Script;
use Phalcon\Builder\BuilderException;

class MyModel extends Phalcon\Commands\Builtin\Model {

    public function run($parameters){
        $config = include __DIR__.'/../app/config/config.php';
        include __DIR__.'/../app/config/loader.php';
        //取得Twm\Db\Adapter\Pdo\Mssql定义,让ModelBuilder支持
        //$adapter = new Adapter($config->database);
        //$adapter->close();



        $name = $this->getOption(array('name', 1));

        $className = Utils::camelize(isset($parameters[1]) ? $parameters[1] : $name);
        $fileName = Utils::uncamelize($className);

        $schema = $this->getOption('schema');

        $modelBuilder = new MyModelBuilder(
            array(
                'name'              => $name,
                'schema'            => $schema,
                'className'         => $className,
                'fileName'          => $fileName,
                'genSettersGetters' => $this->isReceivedOption('get-set'),
                'genDocMethods'     => $this->isReceivedOption('doc'),
                'namespace'         => $this->getOption('namespace'),
                'directory'         => $this->getOption('directory'),
                'modelsDir'         => $this->getOption('output'),
                'extends'           => $this->getOption('extends'),
                'excludeFields'     => $this->getOption('excludefields'),
                'force'             => $this->isReceivedOption('force'),
                'mapColumn'         => $this->isReceivedOption('mapcolumn'),
            )
        );

        $modelBuilder->build();
    }

    public function getCommands(){
        return array('model_mssql', 'a model command support mssql adapter');
    }
}

class MyModelBuilder extends Phalcon\Builder\Model {

    public function build()
    {
        $getSource = "
    public function getSource()
    {
        return '%s';
    }
";
        $templateThis = "        \$this->%s(%s);" . PHP_EOL;
        $templateRelation = "        \$this->%s('%s', '%s', '%s', %s);" . PHP_EOL;
        $templateSetter = "
    /**
     * Method to set the value of field %s
     *
     * @param %s \$%s
     * @return \$this
     */
    public function set%s(\$%s)
    {
        \$this->%s = \$%s;

        return \$this;
    }
";

        $templateValidateInclusion = "
        \$this->validate(
            new InclusionIn(
                array(
                    'field'    => '%s',
                    'domain'   => array(%s),
                    'required' => true,
                )
            )
        );";

        $templateValidateEmail = "
        \$this->validate(
            new Email(
                array(
                    'field'    => '%s',
                    'required' => true,
                )
            )
        );";

        $templateValidationFailed = "
        if (\$this->validationHasFailed() == true) {
            return false;
        }";

        $templateAttributes = "
    /**
     *
     * @var %s
     */
    %s \$%s;
";

        $templateGetterMap = "
    /**
     * Returns the value of field %s
     *
     * @return %s
     */
    public function get%s()
    {
        if (\$this->%s) {
            return new %s(\$this->%s);
        } else {
           return null;
        }
    }
";

        $templateGetter = "
    /**
     * Returns the value of field %s
     *
     * @return %s
     */
    public function get%s()
    {
        return \$this->%s;
    }
";

        $templateValidations = "
    /**
     * Validations and business logic
     */
    public function validation()
    {
%s
    }
";

        $templateInitialize = "
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
%s
    }
";

        $templateFind = "
    /**
     * @return %s[]
     */
    public static function find(\$parameters = array())
    {
        return parent::find(\$parameters);
    }

    /**
     * @return %s
     */
    public static function findFirst(\$parameters = array())
    {
        return parent::findFirst(\$parameters);
    }
";

        $templateUse = 'use %s;';
        $templateUseAs = 'use %s as %s;';

        $templateCode = "<?php

%s%s%sclass %s extends %s
{
%s
}
";

        if (!$this->_options['name']) {
            throw new BuilderException("You must specify the table name");
        }

        $path = '';
        if (isset($this->_options['directory'])) {
            if ($this->_options['directory']) {
                $path = $this->_options['directory'] . '/';
            }
        } else {
            $path = '.';
        }

        $config = $this->_getConfig($path);

        if (!isset($this->_options['modelsDir'])) {
            if (!isset($config->application->modelsDir)) {
                throw new BuilderException(
                    "Builder doesn't knows where is the models directory"
                );
            }
            $modelsDir = $config->application->modelsDir;
        } else {
            $modelsDir = $this->_options['modelsDir'];
        }

        $modelsDir = rtrim(rtrim($modelsDir, '/'), '\\') . DIRECTORY_SEPARATOR;

        if ($this->isAbsolutePath($modelsDir) == false) {
            $modelPath = $path . DIRECTORY_SEPARATOR . $modelsDir;
        } else {
            $modelPath = $modelsDir;
        }

        $methodRawCode = array();
        $className = $this->_options['className'];
        $modelPath .= $className . '.php';

        if (file_exists($modelPath)) {
            if (!$this->_options['force']) {
                throw new BuilderException(
                    "The model file '" . $className .
                    ".php' already exists in models dir"
                );
            }
        }

        if (!isset($config->database)) {
            throw new BuilderException(
                "Database configuration cannot be loaded from your config file"
            );
        }

        if (!isset($config->database->adapter)) {
            throw new BuilderException(
                "Adapter was not found in the config. " .
                "Please specify a config variable [database][adapter]"
            );
        }

        if (isset($this->_options['namespace'])) {
            $namespace = 'namespace ' . $this->_options['namespace'] . ';'
                . PHP_EOL . PHP_EOL;
            $methodRawCode[] = sprintf($getSource, $this->_options['name']);
        } else {
            $namespace = '';
        }

        $useSettersGetters = $this->_options['genSettersGetters'];
        if (isset($this->_options['genDocMethods'])) {
            $genDocMethods = $this->_options['genDocMethods'];
        } else {
            $genDocMethods = false;
        }

        $adapter = $config->database->adapter;

        if (is_object($config->database)) {
            $configArray = $config->database->toArray();
        } else {
            $configArray = $config->database;
        }

        // An array for use statements
        $uses = array();

        $adapterName = $adapter;
        unset($configArray['adapter']);
        $db = new $adapterName($configArray);

        $initialize = array();

/*        if (isset($this->_options['schema'])) {
            if ($this->_options['schema'] != $config->database->dbname) {
                $initialize[] = sprintf(
                    $templateThis, 'setSchema', '"' . $this->_options['schema'] . '"'
                );
            }
            $schema = $this->_options['schema'];
        } elseif ($adapter == 'Postgresql') {
            $schema = 'public';
            $initialize[] = sprintf(
                $templateThis, 'setSchema', '"' . $this->_options['schema'] . '"'
            );
        } elseif ($adapter)

        else {
            $schema = $config->database->dbname;
        }*/

        if ($this->_options['fileName'] != $this->_options['name']) {
            $initialize[] = sprintf(
                $templateThis, 'setSource',
                '\'' . $this->_options['name'] . '\''
            );
        }
        $schema = 'dbo';
        $table = $this->_options['name'];
        if ($db->tableExists($table, $schema)) {
            $fields = $db->describeColumns($table, $schema);
        } else {
            throw new BuilderException('Table "' . $table . '" does not exist');
        }

        if (isset($this->_options['hasMany'])) {
            if (count($this->_options['hasMany'])) {
                foreach ($this->_options['hasMany'] as $relation) {
                    if (is_string($relation['fields'])) {
                        $entityName = $relation['camelizedName'];
                        if (isset($this->_options['namespace'])) {
                            $entityNamespace = "{$this->_options['namespace']}\\";
                            $relation['options']['alias'] = $entityName;
                        } else {
                            $entityNamespace = '';
                        }
                        $initialize[] = sprintf(
                            $templateRelation,
                            'hasMany',
                            $relation['fields'],
                            $entityNamespace . $entityName,
                            $relation['relationFields'],
                            $this->_buildRelationOptions(isset($relation['options']) ? $relation["options"] : NULL)
                        );
                    }
                }
            }
        }

        if (isset($this->_options['belongsTo'])) {
            if (count($this->_options['belongsTo'])) {
                foreach ($this->_options['belongsTo'] as $relation) {
                    if (is_string($relation['fields'])) {
                        $entityName = $relation['referencedModel'];
                        if (isset($this->_options['namespace'])) {
                            $entityNamespace = "{$this->_options['namespace']}\\";
                            $relation['options']['alias'] = $entityName;
                        } else {
                            $entityNamespace = '';
                        }
                        $initialize[] = sprintf(
                            $templateRelation,
                            'belongsTo',
                            $relation['fields'],
                            $entityNamespace . $entityName,
                            $relation['relationFields'],
                            $this->_buildRelationOptions(isset($relation['options']) ? $relation["options"] : NULL)
                        );
                    }
                }
            }
        }

        $alreadyInitialized = false;
        $alreadyValidations = false;
        if (file_exists($modelPath)) {
            try {
                $possibleMethods = array();
                if ($useSettersGetters) {
                    foreach ($fields as $field) {
                        $methodName = Utils::camelize($field->getName());
                        $possibleMethods['set' . $methodName] = true;
                        $possibleMethods['get' . $methodName] = true;
                    }
                }

                require $modelPath;

                $linesCode = file($modelPath);
                $fullClassName = $this->_options['className'];
                if (isset($this->_options['namespace'])) {
                    $fullClassName = $this->_options['namespace'] . '\\' . $fullClassName;
                }
                $reflection = new \ReflectionClass($fullClassName);
                foreach ($reflection->getMethods() as $method) {
                    if ($method->getDeclaringClass()->getName() == $fullClassName) {
                        $methodName = $method->getName();
                        if (!isset($possibleMethods[$methodName])) {
                            $methodRawCode[$methodName] = join(
                                '',
                                array_slice(
                                    $linesCode,
                                    $method->getStartLine() - 1,
                                    $method->getEndLine() - $method->getStartLine() + 1
                                )
                            );
                        } else {
                            continue;
                        }
                        if ($methodName == 'initialize') {
                            $alreadyInitialized = true;
                        } else {
                            if ($methodName == 'validation') {
                                $alreadyValidations = true;
                            }
                        }
                    }
                }
            } catch (\ReflectionException $e) {
            }
        }

        $validations = array();
        foreach ($fields as $field) {
            if ($field->getType() === Column::TYPE_CHAR) {
                $domain = array();
                if (preg_match('/\((.*)\)/', $field->getType(), $matches)) {
                    foreach (explode(',', $matches[1]) as $item) {
                        $domain[] = $item;
                    }
                }
                if (count($domain)) {
                    $varItems = join(', ', $domain);
                    $validations[] = sprintf(
                        $templateValidateInclusion, $field->getName(), $varItems
                    );
                }
            }
            if ($field->getName() == 'email') {
                $validations[] = sprintf(
                    $templateValidateEmail, $field->getName()
                );
                $uses[] = sprintf(
                    $templateUseAs,
                    'Phalcon\Mvc\Model\Validator\Email',
                    'Email'
                );
            }
        }
        if (count($validations)) {
            $validations[] = $templateValidationFailed;
        }

        /**
         * Check if there has been an extender class
         */
        $extends = '\\Phalcon\\Mvc\\Model';
        if (isset($this->_options['extends'])) {
            if (!empty($this->_options['extends'])) {
                $extends = $this->_options['extends'];
            }
        }

        /**
         * Check if there have been any excluded fields
         */
        $exclude = array();
        if (isset($this->_options['excludeFields'])) {
            if (!empty($this->_options['excludeFields'])) {
                $keys = explode(',', $this->_options['excludeFields']);
                if (count($keys) > 0) {
                    foreach ($keys as $key) {
                        $exclude[trim($key)] = '';
                    }
                }
            }
        }

        $attributes = array();
        $setters = array();
        $getters = array();
        foreach ($fields as $field) {
            $type = $this->getPHPType($field->getType());
            if ($useSettersGetters) {

                if (!array_key_exists(strtolower($field->getName()), $exclude)) {
                    $attributes[] = sprintf(
                        $templateAttributes, $type, 'protected', $field->getName()
                    );
                    $setterName = Utils::camelize($field->getName());
                    $setters[] = sprintf(
                        $templateSetter,
                        $field->getName(),
                        $type,
                        $field->getName(),
                        $setterName,
                        $field->getName(),
                        $field->getName(),
                        $field->getName()
                    );

                    if (isset($this->_typeMap[$type])) {
                        $getters[] = sprintf(
                            $templateGetterMap,
                            $field->getName(),
                            $type,
                            $setterName,
                            $field->getName(),
                            $this->_typeMap[$type],
                            $field->getName()
                        );
                    } else {
                        $getters[] = sprintf(
                            $templateGetter,
                            $field->getName(),
                            $type,
                            $setterName,
                            $field->getName()
                        );
                    }
                }
            } else {
                $attributes[] = sprintf(
                    $templateAttributes, $type, 'public', $field->getName()
                );
            }
        }

        if ($alreadyValidations == false) {
            if (count($validations) > 0) {
                $validationsCode = sprintf(
                    $templateValidations, join('', $validations)
                );
            } else {
                $validationsCode = '';
            }
        } else {
            $validationsCode = '';
        }

        if ($alreadyInitialized == false) {
            if (count($initialize) > 0) {
                $initCode = sprintf(
                    $templateInitialize,
                    rtrim(join('', $initialize))
                );
            } else {
                $initCode = '';
            }
        } else {
            $initCode = '';
        }

        $license = '';
        if (file_exists('license.txt')) {
            $license = trim(file_get_contents('license.txt')) . PHP_EOL . PHP_EOL;
        }

        $content = join('', $attributes);

        if ($useSettersGetters) {
            $content .= join('', $setters)
                . join('', $getters);
        }

        $content .= $validationsCode . $initCode;
        foreach ($methodRawCode as $methodCode) {
            $content .= $methodCode;
        }

        if ($genDocMethods) {
            $content .= sprintf($templateFind, $className, $className);
        }

        if (isset($this->_options['mapColumn'])) {
            $content .= $this->_genColumnMapCode($fields);
        }

        $str_use = '';
        if (!empty($uses)) {
            $str_use = implode(PHP_EOL, $uses) . PHP_EOL . PHP_EOL;
        }

        $code = sprintf(
            $templateCode,
            $license,
            $namespace,
            $str_use,
            $className,
            $extends,
            $content
        );

        if (!@file_put_contents($modelPath, $code)) {
            throw new BuilderException("Unable to write to '$modelPath'");
        }

        if ($this->isConsole()) {
            $this->_notifySuccess('Model "' . $this->_options['name'] . '" was successfully created.');
        }
    }

    private function _genColumnMapCode($fields)
    {
        $template = '
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            %s
        );
    }
';
        $contents = array();
        foreach ($fields as $field) {
            $name = $field->getName();
            $contents[] = sprintf('\'%s\' => \'%s\'', $name, $name);
        }

        return sprintf($template, join(", \n            ", $contents));
    }
}