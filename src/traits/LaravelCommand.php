<?php

namespace yangweijie\thinkElectron\traits;

use InvalidArgumentException;
use ReflectionException;
use ReflectionMethod;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\console\output\driver\Buffer;

/**
 * Laravel命令适配器
 * 
 * 此trait用于将Laravel风格的命令适配到ThinkPHP的命令系统
 * 使用方法：
 * 1. 将原Laravel命令类的继承改为 think\console\Command
 * 2. 使用此trait
 * 3. 保留原有的signature和description属性
 */
trait LaravelCommand
{
    /**
     * 命令签名
     *
     * @var string
     */
    protected string $signature;

    /**
     * 命令描述
     *
     * @var string
     */
    protected string $description;

    /**
     * 配置命令
     *
     * 自动将Laravel风格的signature转换为ThinkPHP的configure方法配置
     */
    protected function configure(): void
    {
        if (empty($this->signature)) {
            throw new InvalidArgumentException('命令签名不能为空');
        }

        // 解析命令签名
        $parts = explode(' ', $this->signature);
        $name = array_shift($parts);

        // 设置命令名和描述
        $this->setName($name)
            ->setDescription($this->description ?? '');

        // 解析参数和选项
        $this->parseSignature($parts);
    }

    /**
     * 解析命令签名中的参数和选项
     *
     * @param array $parts 签名部分
     */
    protected function parseSignature(array $parts): void
    {
        foreach ($parts as $part) {
            // 处理选项 --option 或 {--option}
            if (str_starts_with($part, '--') || (str_starts_with($part, '{--') && str_ends_with($part, '}'))) {
                $this->addSignatureOption($part);
            }
            // 处理参数 {argument}
            elseif (str_starts_with($part, '{') && str_ends_with($part, '}')) {
                $this->addSignatureArgument($part);
            }
        }
    }

    /**
     * 添加签名中的选项
     *
     * @param string $option 选项定义
     */
    protected function addSignatureOption(string $option): void
    {
        // 移除花括号
        $option = trim($option, '{}');
        
        // 移除前导的--
        if (str_starts_with($option, '--')) {
            $option = substr($option, 2);
        }

        // 解析选项名称、短名称和描述
        $parts = explode('|', $option);
        $name = $parts[0];
        $shortName = $parts[1] ?? null;
        
        // 检查是否有描述
        $description = '';
        if (str_contains($name, ' : ')) {
            [$name, $description] = explode(' : ', $name, 2);
        }

        // 检查是否是必需选项
        $mode = Option::VALUE_OPTIONAL;
        if (str_ends_with($name, '=')) {
            $name = rtrim($name, '=');
            $mode = Option::VALUE_REQUIRED;
        }

        // 添加选项
        $this->addOption($name, $shortName, $mode, $description);
    }

    /**
     * 添加签名中的参数
     *
     * @param string $argument 参数定义
     */
    protected function addSignatureArgument(string $argument): void
    {
        // 移除花括号
        $argument = trim($argument, '{}');

        // 解析参数名称和描述
        $description = '';
        if (str_contains($argument, ' : ')) {
            [$argument, $description] = explode(' : ', $argument, 2);
        }

        // 检查是否是可选参数
        $mode = Argument::REQUIRED;
        if (str_starts_with($argument, '?')) {
            $argument = substr($argument, 1);
            $mode = Argument::OPTIONAL;
        }

        // 添加参数
        $this->addArgument($argument, $mode, $description);
    }

    /**
     * 执行命令
     *
     * 将ThinkPHP的Input和Output对象适配到Laravel命令的handle方法
     * @throws ReflectionException
     */
    protected function execute(Input $input, Output $output)
    {
        // 如果存在handle方法，则调用它
        if (method_exists($this, 'handle')) {
            // 获取handle方法的参数
            $reflectionMethod = new ReflectionMethod($this, 'handle');
            $parameters = $reflectionMethod->getParameters();
            
            $args = [];
            foreach ($parameters as $parameter) {
                $paramType = $parameter->getType();
                
                // 如果参数类型是Input或Output，则传入对应对象
                if ($paramType && !$paramType->isBuiltin()) {
                    $typeName = $paramType->getName();
                    if (is_a(Input::class, $typeName, true)) {
                        $args[] = $input;
                    } elseif (is_a(Output::class, $typeName, true)) {
                        $args[] = $output;
                    } else {
                        // 尝试从容器中解析其他依赖
                        $args[] = app($typeName);
                    }
                } else {
                    // 对于其他类型的参数，传入null
                    $args[] = null;
                }
            }
            
            // 调用handle方法
            return $reflectionMethod->invokeArgs($this, $args);
        }
        
        return 0;
    }

    /**
     * 获取参数值
     *
     * @param string|null $key 参数名
     * @return mixed
     */
    public function argument(string $key = null): mixed
    {
        if (is_null($key)) {
            return $this->input->getArguments();
        }

        return $this->input->getArgument($key);
    }

    /**
     * 获取选项值
     *
     * @param string|null $key 选项名
     * @return mixed
     */
    public function option(string $key = null): mixed
    {
        if (is_null($key)) {
            return $this->input->getOptions();
        }

        return $this->input->getOption($key);
    }

    /**
     * 输出一行信息
     *
     * @param string $string 输出内容
     * @param string|null $style 样式
     * @return void
     */
    public function line(string $string, string $style = null): void
    {
        $styled = $style ? "<{$style}>{$string}</{$style}>" : $string;
        $this->output->writeln($styled);
    }

    /**
     * 输出信息
     *
     * @param string $string 输出内容
     * @return void
     */
    public function info(string $string): void
    {
        $this->line($string, 'info');
    }

    /**
     * 输出成功信息
     *
     * @param string $string 输出内容
     * @return void
     */
    public function success(string $string): void
    {
        $this->line($string, 'success');
    }

    /**
     * 输出错误信息
     *
     * @param string $string 输出内容
     * @return void
     */
    public function error(string $string): void
    {
        $this->line($string, 'error');
    }

    /**
     * 输出警告信息
     *
     * @param string $string 输出内容
     * @return void
     */
    public function warn(string $string): void
    {
        $this->line($string, 'warning');
    }

    /**
     * 询问确认
     *
     * @param string $question 问题
     * @param bool $default 默认值
     * @return bool
     */
    public function confirm(string $question, bool $default = false): bool
    {
        return $this->output->confirm($this->input, $question, $default);
    }

    /**
     * 询问选择
     *
     * @param string $question 问题
     * @param array $choices 选项
     * @param mixed|null $default 默认值
     * @return mixed
     */
    public function choice(string $question, array $choices, mixed $default = null): mixed
    {
        return $this->output->choice($this->input, $question, $choices, $default);
    }

    /**
     * 询问输入
     *
     * @param string $question 问题
     * @param string|null $default 默认值
     * @return string
     */
    public function ask(string $question, string $default = null): string
    {
        return $this->output->ask($this->input, $question, $default);
    }

    /**
     * 获取输出对象
     *
     * @return Output
     */
    public function output(): Output
    {
        return $this->output;
    }

    /**
     * 调用另一个命令
     *
     * 此方法兼容Laravel的Artisan::call方法，将调用转发到ThinkPHP的命令系统
     *
     * @param string $command 命令名称
     * @param array $arguments 命令参数和选项
     * @return Output|Buffer 命令执行的返回值
     */
    public function call(string $command, array $arguments = [], string $driver = 'buffer'): Buffer|Output
    {
        // 获取应用实例
        $app = app();
        
        // 创建命令行应用实例
        $console = $app->console;
        
        // 准备参数数组
        $parameters = [$command];
        
        // 添加参数
        foreach ($arguments as $key => $value) {
            if (is_numeric($key)) {
                // 位置参数
                $parameters[] = $value;
            } else {
                // 选项参数
                $key = '--' . ltrim($key, '-');
                
                if (is_bool($value)) {
                    if ($value) {
                        $parameters[] = $key;
                    }
                } else {
                    $parameters[] = $key . '=' . $value;
                }
            }
        }
        
        // 执行命令并返回结果
        return $console->call($command, $parameters, $driver);
    }
}