<?php

namespace NetPhp\Core;

/**
 * Wrapper for full PHP-.Net interoperability.
 */
class MagicWrapper extends ComProxy {

  // @var ResolvedClass $type_data
  private $type_data;

  /**
   * Summary of Type
   * @param ResolvedClass $type 
   * @return MagicWrapper
   */
  public function Type($type) {
    $this->type_data = $type;
    return $this;
  }
  
  protected function __construct() {
    $this->_Instantiate(Constants::ASSEMBLY, Constants::MW_CLASS);
  }
  
  /**
   * Summary of Get
   * @return MagicWrapper
   */
  public static function Get() {
    return new MagicWrapper();
  }
  
  /**
   * Summary of Wrap
   * @param mixed $source 
   * @return MagicWrapper
   */
  public function Wrap($source) {
    $this->_Wrap($source);
    return $this;
  }
  
  public function UnPack() {
    return $this->host;
  }

  /**
   * Summary of CallMethod
   * @param mixed $method 
   * @param mixed $args 
   * @return MagicWrapper
   */
  public function CallMethod($method, $args) {
    // Wrap a new Magic Wrapper around the result.
    $result = static::Get()->Wrap($this->host->CallMethod($method, $args));
    self::ManageExceptions();
    return $result;
  }
  
  /**
   * Summary of PropertySet
   * @param mixed $property 
   * @param mixed $value 
   */
  public function PropertySet($property, $value) {
    $this->host->PropertySet($property, $value);
    self::ManageExceptions();
  }
  
  /**
   * Summary of PropertyGet
   * @param mixed $property 
   * @return MagicWrapper
   */
  public function PropertyGet($property) {
    $result = $this->host->PropertyGet($property);
    self::ManageExceptions();
    return static::Get()->Wrap($result);
  }
  
  /**
   * Create an internal instance of the provided .Net type.
   *
   * @param mixed $assemblyPath
   *  Full Path to the .dll file to load.
   *
   * @param mixed $name 
   *  Full qualified name of the .Net type inside the assembly.
   *
   * @param mixed $args 
   *  Arguments to pass for the type constructor.
   */
  public function Instantiate( ...$args) {
    $assembly = $this->type_data->assemblyFullQualifiedName;
    if (file_exists($assembly)) {
      $this->host->InstantiateFromAssembly($assembly, $this->type_data->classFullQualifiedName, ...$args);
    }
    else {
      $this->host->InstantiateFromFullQualifiedName($assembly, $this->type_data->classFullQualifiedName, ...$args);
    }
    
    self::ManageExceptions();
  }
  
  /**
   * Wraps over an Enum value
   *
   * @param mixed $assemblyPath
   *  Full Path to the .dll file to load.
   *
   * @param mixed $name 
   *  Full qualified name of the .Net type inside the assembly.
   *
   * @param mixed $value 
   *  The Enum value to wrap over.
   */
  public function Enum($value) {
    $this->host->Enum($this->type_data->assemblyFullQualifiedName, $this->type_data->classFullQualifiedName, $value);
    self::ManageExceptions();
  }
  
  /**
   * Summary of WrappedType
   */
  public function WrappedType() {
    $result = (string) $this->host->WrappedType();
    self::ManageExceptions();
    return $result;
  }
  
  
  /**
   * Get the internal hosted object!
   */
  public function UnWrap() {
    $result = $this->host->UnWrap();
    self::ManageExceptions();
    return $result;
  }
}