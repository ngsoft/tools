# Documentation

## Table of Contents

| Method | Description |
|--------|-------------|
| [**CacheLock**](#CacheLock) | Use a cache pool to manage your locks |
| [CacheLock::__construct](#CacheLock__construct) |  |
| [CacheLock::forceRelease](#CacheLockforceRelease) | {@inheritdoc} |
| [**CharMap**](#CharMap) | A Multibyte/byte string convertion Map |
| [CharMap::create](#CharMapcreate) | Create a new CharMap |
| [CharMap::getCharOffset](#CharMapgetCharOffset) | Get character offset from byte offsetReturns -1 on failure |
| [CharMap::getByteOffset](#CharMapgetByteOffset) | Get byte offset from character Offsetreturns -1 on failure |
| [CharMap::__construct](#CharMap__construct) | Create a new CharMap |
| [CharMap::convertByteOffset](#CharMapconvertByteOffset) | Get Character Offset from Byte Offset |
| [CharMap::convertCharacterOffset](#CharMapconvertCharacterOffset) | Get Byte offset from Character Offset |
| [CharMap::getLength](#CharMapgetLength) | Get number of characters |
| [CharMap::getSize](#CharMapgetSize) | Get number of bytes |
| [CharMap::count](#CharMapcount) |  |
| [CharMap::isEmpty](#CharMapisEmpty) |  |
| [CharMap::toString](#CharMaptoString) |  |
| [CharMap::__toString](#CharMap__toString) |  |
| [CharMap::__unserialize](#CharMap__unserialize) |  |
| [CharMap::__serialize](#CharMap__serialize) |  |
| [**CircularDependencyException**](#CircularDependencyException) |  |
| [**Container**](#Container) |  |
| [Container::__construct](#Container__construct) |  |
| [Container::alias](#Containeralias) | Alias an entry to a different name |
| [Container::has](#Containerhas) | {@inheritdoc} |
| [Container::get](#Containerget) | {@inheritdoc} |
| [Container::make](#Containermake) | Resolves an entry by its name. If given a class name, it will return a fresh instance of that class. |
| [Container::call](#Containercall) | Call the given function using the given parameters. |
| [Container::register](#Containerregister) | Register a service |
| [Container::set](#Containerset) | Add a definition to the container |
| [Container::setMany](#ContainersetMany) | Adds multiple definitions |
| [Container::addContainerResolver](#ContaineraddContainerResolver) | Adds an handler to manage entry resolution (afyer params have been resolved) |
| [Container::__debugInfo](#Container__debugInfo) |  |
| [**Container**](#Container) |  |
| [Container::alias](#Containeralias) | Alias an entry to a different name |
| [Container::has](#Containerhas) | Returns true if the container can return an entry for the given identifier. |
| [Container::get](#Containerget) | Finds an entry of the container by its identifier and returns it. |
| [Container::make](#Containermake) | Resolves an entry by its name. If given a class name, it will return a fresh instance of that class. |
| [Container::call](#Containercall) | Call the given function using the given parameters. |
| [Container::register](#Containerregister) | Register a service |
| [Container::set](#Containerset) | Add a definition to the container |
| [Container::setMany](#ContainersetMany) | Adds multiple definitions |
| [Container::addContainerResolver](#ContaineraddContainerResolver) | Adds an handler to manage entry resolution (after params have been resolved) |
| [**ContainerError**](#ContainerError) |  |
| [**Directory**](#Directory) | Manages a directory |
| [Directory::scanFiles](#DirectoryscanFiles) | Scan files in a directory |
| [Directory::scanFilesArray](#DirectoryscanFilesArray) |  |
| [Directory::cwd](#Directorycwd) |  |
| [Directory::pushd](#Directorypushd) | Change the current active directory and stores the last position |
| [Directory::popd](#Directorypopd) | Restore the last active directory position and returns it |
| [Directory::__construct](#Directory__construct) |  |
| [Directory::copy](#Directorycopy) | Copy Directory to another location |
| [Directory::delete](#Directorydelete) | Recursively delete a directory. |
| [Directory::exists](#Directoryexists) | Checks if directory exists |
| [Directory::isEmpty](#DirectoryisEmpty) | Checks if no files |
| [Directory::mkdir](#Directorymkdir) | Create dir |
| [Directory::rmdir](#Directoryrmdir) | Remove dir |
| [Directory::chdir](#Directorychdir) | Change dir |
| [Directory::isCurrentWorkingDir](#DirectoryisCurrentWorkingDir) | Checks if is current active dir |
| [Directory::search](#Directorysearch) | Search for a file recursively using regex, glob or check if filename contains $query |
| [Directory::glob](#Directoryglob) | Executes a glob search inside the directory |
| [Directory::files](#Directoryfiles) | List files inside directory |
| [Directory::allFiles](#DirectoryallFiles) | List files recursively |
| [Directory::directories](#Directorydirectories) | List directories |
| [Directory::getFile](#DirectorygetFile) | Access a file in that directory |
| [Directory::getIterator](#DirectorygetIterator) |  |
| [**EnumUtils**](#EnumUtils) |  |
| [EnumUtils::generateEnumClassPhpDoc](#EnumUtilsgenerateEnumClassPhpDoc) | Generates Doc Comment for magic static methods |
| [EnumUtils::addPhpDocToEnumClass](#EnumUtilsaddPhpDocToEnumClass) | Auto Generates static methods doc blocks for enums |
| [**FacadeUtils**](#FacadeUtils) |  |
| [FacadeUtils::getClassDocBlocks](#FacadeUtilsgetClassDocBlocks) |  |
| [FacadeUtils::createDocBlock](#FacadeUtilscreateDocBlock) |  |
| [FacadeUtils::createMethodsForInstance](#FacadeUtilscreateMethodsForInstance) |  |
| [FacadeUtils::createMethods](#FacadeUtilscreateMethods) |  |
| [FacadeUtils::createFacadeCode](#FacadeUtilscreateFacadeCode) |  |
| [**File**](#File) | Manages a File |
| [File::__construct](#File__construct) |  |
| [File::__destruct](#File__destruct) |  |
| [File::getDirectory](#FilegetDirectory) | Get file directory |
| [File::exists](#Fileexists) | Checks if file exists and is regular file |
| [File::isModified](#FileisModified) | Check if crc checksum has changed |
| [File::unlink](#Fileunlink) | Deletes the file |
| [File::copy](#Filecopy) | Copy File |
| [File::delete](#Filedelete) | Delete the file |
| [File::require](#Filerequire) | Includes file as php file |
| [File::name](#Filename) | Get file name without extension |
| [File::extension](#Fileextension) | Get the last file extension |
| [File::hash](#Filehash) | Get CRC32 Checksum |
| [File::touch](#Filetouch) | Sets access and modification time of file |
| [File::getContents](#FilegetContents) | Loads file as an Iterator |
| [File::createContents](#FilecreateContents) | Creates file contents |
| [File::read](#Fileread) | Loads file |
| [File::readAsArray](#FilereadAsArray) | Read file as array of lines |
| [File::readJson](#FilereadJson) | Decodes json file |
| [File::write](#Filewrite) | Save File |
| [File::writeJson](#FilewriteJson) | Dumps data to json |
| [File::getIterator](#FilegetIterator) |  |
| [File::lock](#Filelock) | Locks file access on concurrent requests |
| [File::__debugInfo](#File__debugInfo) |  |
| [**FileContents**](#FileContents) |  |
| [FileContents::__construct](#FileContents__construct) |  |
| [FileContents::refresh](#FileContentsrefresh) | Reorganize lines |
| [FileContents::reload](#FileContentsreload) | Reloads file contents |
| [FileContents::clear](#FileContentsclear) | Clears the contents |
| [FileContents::map](#FileContentsmap) | Run the callable with all the lines and replaces the contents with the return value |
| [FileContents::filter](#FileContentsfilter) | Run a callable for all the line and removes line that does not pass the test |
| [FileContents::save](#FileContentssave) | Save file contents |
| [FileContents::readLine](#FileContentsreadLine) | Reads a line |
| [FileContents::write](#FileContentswrite) | Replaces the entire contents |
| [FileContents::writeLine](#FileContentswriteLine) | replaces / adds a line |
| [FileContents::insertLine](#FileContentsinsertLine) | Insert a lineif no offset defined will add to the begining of the file, if out of range will be added to the end of the file |
| [FileContents::removeLine](#FileContentsremoveLine) | Delete a line, also reorganize lines |
| [FileContents::offsetExists](#FileContentsoffsetExists) | {@inheritdoc} |
| [FileContents::offsetGet](#FileContentsoffsetGet) | {@inheritdoc} |
| [FileContents::offsetSet](#FileContentsoffsetSet) | {@inheritdoc} |
| [FileContents::offsetUnset](#FileContentsoffsetUnset) | {@inheritdoc} |
| [FileContents::isEmpty](#FileContentsisEmpty) |  |
| [FileContents::count](#FileContentscount) | {@inheritdoc} |
| [FileContents::getIterator](#FileContentsgetIterator) |  |
| [FileContents::jsonSerialize](#FileContentsjsonSerialize) | {@inheritdoc} |
| [FileContents::__toString](#FileContents__toString) | {@inheritdoc} |
| [FileContents::__serialize](#FileContents__serialize) | {@inheritdoc} |
| [FileContents::__unserialize](#FileContents__unserialize) | {@inheritdoc} |
| [FileContents::__debugInfo](#FileContents__debugInfo) |  |
| [**FileFactory**](#FileFactory) |  |
| [FileFactory::getFile](#FileFactorygetFile) | Get a File instance |
| [FileFactory::getDirectory](#FileFactorygetDirectory) | Get a Directory instance |
| [FileFactory::getFileContents](#FileFactorygetFileContents) | Get File Contents |
| [**FileList**](#FileList) | File list Iterator |
| [FileList::create](#FileListcreate) |  |
| [FileList::append](#FileListappend) | Adds a file to the list |
| [FileList::files](#FileListfiles) | Returns only files |
| [FileList::directories](#FileListdirectories) | Returns only directories |
| [FileList::filter](#FileListfilter) | Filter results using callable |
| [FileList::toArray](#FileListtoArray) | Returns files realpaths |
| [FileList::isEmpty](#FileListisEmpty) |  |
| [FileList::count](#FileListcount) |  |
| [FileList::getIterator](#FileListgetIterator) |  |
| [FileList::keys](#FileListkeys) |  |
| [FileList::values](#FileListvalues) |  |
| [FileList::__serialize](#FileList__serialize) |  |
| [FileList::__unserialize](#FileList__unserialize) |  |
| [FileList::__debugInfo](#FileList__debugInfo) |  |
| [**FileLock**](#FileLock) | Uses php files to create locks |
| [FileLock::__construct](#FileLock__construct) |  |
| [FileLock::forceRelease](#FileLockforceRelease) | {@inheritdoc} |
| [**FileSystem**](#FileSystem) |  |
| [FileSystem::getFile](#FileSystemgetFile) | Get a File instance |
| [FileSystem::getDirectory](#FileSystemgetDirectory) | Get a Directory instance |
| [FileSystem::getFileContents](#FileSystemgetFileContents) | Get File Contents |
| [**FileSystemLock**](#FileSystemLock) | Creates a lock file with the same filename and directory as provided file |
| [FileSystemLock::__construct](#FileSystemLock__construct) |  |
| [FileSystemLock::forceRelease](#FileSystemLockforceRelease) |  |
| [**FixedArray**](#FixedArray) | An array with fixed capacityUses LRU model (Last Recently Used gets removed first)SplFixedArray only works with int offsets (not null or strings) |
| [FixedArray::create](#FixedArraycreate) | Creates a new Fixed Array |
| [FixedArray::__construct](#FixedArray__construct) |  |
| [FixedArray::clear](#FixedArrayclear) |  |
| [FixedArray::getSize](#FixedArraygetSize) | Gets the size of the array. |
| [FixedArray::setSize](#FixedArraysetSize) | Change the size of an array to the new size of size. If size is less than the current array size,any values after the new size will be discarded. |
| [FixedArray::count](#FixedArraycount) | {@inheritdoc} |
| [FixedArray::entries](#FixedArrayentries) | Iterates entries in sort order |
| [FixedArray::keys](#FixedArraykeys) | Returns a new iterable with only the indexes |
| [FixedArray::values](#FixedArrayvalues) | Returns a new iterable with only the values |
| [FixedArray::jsonSerialize](#FixedArrayjsonSerialize) | {@inheritdoc} |
| [FixedArray::offsetExists](#FixedArrayoffsetExists) | {@inheritdoc} |
| [FixedArray::offsetGet](#FixedArrayoffsetGet) | {@inheritdoc} |
| [FixedArray::offsetSet](#FixedArrayoffsetSet) | {@inheritdoc} |
| [FixedArray::offsetUnset](#FixedArrayoffsetUnset) | {@inheritdoc} |
| [FixedArray::__debugInfo](#FixedArray__debugInfo) | {@inheritdoc} |
| [FixedArray::__serialize](#FixedArray__serialize) | {@inheritdoc} |
| [FixedArray::__unserialize](#FixedArray__unserialize) | {@inheritdoc} |
| [FixedArray::__clone](#FixedArray__clone) | {@inheritdoc} |
| [**Inject**](#Inject) |  |
| [Inject::__construct](#Inject__construct) |  |
| [Inject::__toString](#Inject__toString) |  |
| [**InjectProperties**](#InjectProperties) | Scans for #[Inject] attribute on the loaded class properties |
| [InjectProperties::resolve](#InjectPropertiesresolve) | Resolves an entry from the container |
| [InjectProperties::getDefaultPriority](#InjectPropertiesgetDefaultPriority) | Set the default priority |
| [**InnerFacade**](#InnerFacade) |  |
| [InnerFacade::boot](#InnerFacadeboot) | Starts the container |
| [InnerFacade::registerServiceProvider](#InnerFacaderegisterServiceProvider) |  |
| [InnerFacade::getResovedInstance](#InnerFacadegetResovedInstance) |  |
| [InnerFacade::setResolvedInstance](#InnerFacadesetResolvedInstance) |  |
| [InnerFacade::getContainer](#InnerFacadegetContainer) |  |
| [InnerFacade::setContainer](#InnerFacadesetContainer) |  |
| [**JsonObject**](#JsonObject) | A Json object that syncs data with a json file concurently |
| [JsonObject::fromJsonFile](#JsonObjectfromJsonFile) |  |
| [**Lock**](#Lock) |  |
| [Lock::createFileLock](#LockcreateFileLock) | Create a Php File Lock |
| [Lock::createFileSystemLock](#LockcreateFileSystemLock) |  |
| [Lock::createSQLiteLock](#LockcreateSQLiteLock) | Create a SQLite Lock |
| [Lock::createNoLock](#LockcreateNoLock) | Create a NoLock |
| [Lock::createCacheLock](#LockcreateCacheLock) | Create a lock using a PSR-6 Cache |
| [Lock::createSimpleCacheLock](#LockcreateSimpleCacheLock) | Create a lock using a PSR-16 Cache |
| [**LockFactory**](#LockFactory) |  |
| [LockFactory::__construct](#LockFactory__construct) |  |
| [LockFactory::createFileLock](#LockFactorycreateFileLock) | Create a Php File Lock |
| [LockFactory::createFileSystemLock](#LockFactorycreateFileSystemLock) | Create a .lock file inside the dame directory as the provided file |
| [LockFactory::createSQLiteLock](#LockFactorycreateSQLiteLock) | Create a SQLite Lock |
| [LockFactory::createNoLock](#LockFactorycreateNoLock) | Create a NoLock |
| [LockFactory::createCacheLock](#LockFactorycreateCacheLock) | Create a lock using a PSR-6 Cache |
| [LockFactory::createSimpleCacheLock](#LockFactorycreateSimpleCacheLock) | Create a lock using a PSR-16 Cache |
| [**LockServiceProvider**](#LockServiceProvider) |  |
| [LockServiceProvider::provides](#LockServiceProviderprovides) | Get the services provided by the provider. |
| [LockServiceProvider::register](#LockServiceProviderregister) | Register the service into the container |
| [**LockTimeout**](#LockTimeout) |  |
| [**Logger**](#Logger) |  |
| [Logger::log](#Loggerlog) | Logs with an arbitrary level. |
| [Logger::emergency](#Loggeremergency) | System is unusable. |
| [Logger::alert](#Loggeralert) | Action must be taken immediately. |
| [Logger::critical](#Loggercritical) | Critical conditions. |
| [Logger::error](#Loggererror) | Runtime errors that do not require immediate action but should typicallybe logged and monitored. |
| [Logger::warning](#Loggerwarning) | Exceptional occurrences that are not errors. |
| [Logger::notice](#Loggernotice) | Normal but significant events. |
| [Logger::info](#Loggerinfo) | Interesting events. |
| [Logger::debug](#Loggerdebug) | Detailed debug information. |
| [**LoggerAwareResolver**](#LoggerAwareResolver) | Injects Logger |
| [LoggerAwareResolver::resolve](#LoggerAwareResolverresolve) | Resolves an entry from the container |
| [LoggerAwareResolver::getDefaultPriority](#LoggerAwareResolvergetDefaultPriority) | Set the default priority |
| [**Map**](#Map) | The Map object holds key-value pairs and remembers the original insertion order of the keys. |
| [Map::clear](#Mapclear) | The clear() method removes all elements from a Map object. |
| [Map::delete](#Mapdelete) | The delete() method removes the specified element from a Map object by key. |
| [Map::get](#Mapget) | The get() method returns a specified element from a Map object. |
| [Map::search](#Mapsearch) | The search() method returns the first key match from a value |
| [Map::set](#Mapset) | The set() method adds or updates an element with a specified key and a value to a Map object. |
| [Map::add](#Mapadd) | The add() method adds an element with a specified key and a value to a Map object if it does&#039;n already exists. |
| [Map::has](#Maphas) | The has() method returns a boolean indicating whether an element with the specified key exists or not. |
| [Map::keys](#Mapkeys) | The keys() method returns a new iterator object that contains the keys for each element in the Map object in insertion order |
| [Map::values](#Mapvalues) | The values() method returns a new iterator object that contains the values for each element in the Map object in insertion order. |
| [Map::entries](#Mapentries) | The entries() method returns a new iterator object that contains the [key, value] pairs for each element in the Map object in insertion order. |
| [Map::forEach](#MapforEach) | The forEach() method executes a provided function once per each key/value pair in the Map object, in insertion order. |
| [Map::offsetExists](#MapoffsetExists) | {@inheritdoc} |
| [Map::offsetGet](#MapoffsetGet) | {@inheritdoc} |
| [Map::offsetSet](#MapoffsetSet) | {@inheritdoc} |
| [Map::offsetUnset](#MapoffsetUnset) | {@inheritdoc} |
| [Map::count](#Mapcount) | {@inheritdoc} |
| [Map::jsonSerialize](#MapjsonSerialize) | {@inheritdoc} |
| [Map::__debugInfo](#Map__debugInfo) | {@inheritdoc} |
| [Map::__serialize](#Map__serialize) | {@inheritdoc} |
| [Map::__unserialize](#Map__unserialize) | {@inheritdoc} |
| [Map::__clone](#Map__clone) | {@inheritdoc} |
| [**NoLock**](#NoLock) | NullLock |
| [NoLock::acquire](#NoLockacquire) | Acquires the lock. |
| [NoLock::forceRelease](#NoLockforceRelease) | {@inheritdoc} |
| [NoLock::isAcquired](#NoLockisAcquired) | Returns whether or not the lock is acquired. |
| [NoLock::release](#NoLockrelease) | Release the lock. |
| [**NotFound**](#NotFound) |  |
| [NotFound::for](#NotFoundfor) |  |
| [**NullServiceProvider**](#NullServiceProvider) |  |
| [NullServiceProvider::__construct](#NullServiceProvider__construct) |  |
| [NullServiceProvider::provides](#NullServiceProviderprovides) | Get the services provided by the provider. |
| [NullServiceProvider::register](#NullServiceProviderregister) | Register the service into the container |
| [**OwnedList**](#OwnedList) | Simulates one to many relationships found in databases |
| [OwnedList::create](#OwnedListcreate) | Creates a new OwnedList for the given value |
| [OwnedList::__construct](#OwnedList__construct) |  |
| [OwnedList::add](#OwnedListadd) | Adds a relationship between current value and the given value |
| [OwnedList::delete](#OwnedListdelete) | Removes a relationship between current value and the given value |
| [OwnedList::has](#OwnedListhas) | Checks if a relationship exists between current value and the given value |
| [OwnedList::clear](#OwnedListclear) | Removes all relationships |
| [OwnedList::entries](#OwnedListentries) | Iterates entries |
| [OwnedList::values](#OwnedListvalues) | Iterates owned values |
| [OwnedList::count](#OwnedListcount) | {@inheritdoc} |
| [OwnedList::offsetExists](#OwnedListoffsetExists) |  |
| [OwnedList::offsetGet](#OwnedListoffsetGet) |  |
| [OwnedList::offsetSet](#OwnedListoffsetSet) |  |
| [OwnedList::offsetUnset](#OwnedListoffsetUnset) |  |
| [OwnedList::jsonSerialize](#OwnedListjsonSerialize) | {@inheritdoc} |
| [OwnedList::__debugInfo](#OwnedList__debugInfo) | {@inheritdoc} |
| [OwnedList::__serialize](#OwnedList__serialize) | {@inheritdoc} |
| [OwnedList::__unserialize](#OwnedList__unserialize) | {@inheritdoc} |
| [OwnedList::__clone](#OwnedList__clone) | {@inheritdoc} |
| [**ParameterResolver**](#ParameterResolver) |  |
| [ParameterResolver::__construct](#ParameterResolver__construct) |  |
| [ParameterResolver::canResolve](#ParameterResolvercanResolve) |  |
| [ParameterResolver::resolve](#ParameterResolverresolve) |  |
| [**PrioritySet**](#PrioritySet) | A Priority Set is a set that sorts entries by priority |
| [PrioritySet::create](#PrioritySetcreate) | Create a new Set |
| [PrioritySet::add](#PrioritySetadd) | The add() method adds a new element with a specified value with a given priority. |
| [PrioritySet::getPriority](#PrioritySetgetPriority) |  |
| [PrioritySet::clear](#PrioritySetclear) | The clear() method removes all elements from a Set object. |
| [PrioritySet::delete](#PrioritySetdelete) | The delete() method removes a specified value from a Set object, if it is in the set. |
| [PrioritySet::entries](#PrioritySetentries) | The entries() method returns a new Iterator object that contains an array of [value, value] for each element in the Set object, in insertion order. |
| [PrioritySet::forEach](#PrioritySetforEach) | The forEach() method executes a provided function once for each value in the Set object, in insertion order. |
| [PrioritySet::has](#PrioritySethas) | The has() method returns a boolean indicating whether an element with the specified value exists in a Set object or not. |
| [PrioritySet::isEmpty](#PrioritySetisEmpty) | Checks if set is empty |
| [PrioritySet::values](#PrioritySetvalues) | The values() method returns a new Iterator object that contains the values for each element in the Set object in insertion order. |
| [PrioritySet::count](#PrioritySetcount) | {@inheritdoc} |
| [PrioritySet::getIterator](#PrioritySetgetIterator) | {@inheritdoc} |
| [PrioritySet::jsonSerialize](#PrioritySetjsonSerialize) | {@inheritdoc} |
| [PrioritySet::__debugInfo](#PrioritySet__debugInfo) | {@inheritdoc} |
| [PrioritySet::__serialize](#PrioritySet__serialize) |  |
| [PrioritySet::__unserialize](#PrioritySet__unserialize) |  |
| [PrioritySet::__clone](#PrioritySet__clone) | {@inheritdoc} |
| [**Range**](#Range) | Returns a sequence of numbers, starting from 0 by default, and increments by 1 (by default), and stops before a specified number. |
| [Range::__construct](#Range__construct) |  |
| [Range::create](#Rangecreate) | Creates a Range |
| [Range::of](#Rangeof) | Get a range for a Countable |
| [Range::__debugInfo](#Range__debugInfo) |  |
| [Range::__toString](#Range__toString) |  |
| [Range::isEmpty](#RangeisEmpty) | Checks if empty range |
| [Range::count](#Rangecount) |  |
| [Range::entries](#Rangeentries) | Iterates entries in sort order |
| [**RegExp**](#RegExp) |  |
| [RegExp::create](#RegExpcreate) | Initialize RegExp |
| [RegExp::__construct](#RegExp__construct) | Initialize RegExp |
| [RegExp::getLastIndex](#RegExpgetLastIndex) | Get the last index |
| [RegExp::setLastIndex](#RegExpsetLastIndex) | Set the Last Index |
| [RegExp::test](#RegExptest) | The test() method executes a search for a match between a regular expression and a specified string. |
| [RegExp::exec](#RegExpexec) | The exec() method executes a search for a match in a specified string. Returns a result array, or null. |
| [RegExp::replace](#RegExpreplace) | The replace() method replaces some or all matches of a this pattern in a string by a replacement,and returns the result of the replacement as a new string. |
| [RegExp::search](#RegExpsearch) | The search() method executes a search for a match between a regular expression and a string. |
| [RegExp::split](#RegExpsplit) | The split() method divides a String into an ordered list of substrings, |
| [RegExp::matchAll](#RegExpmatchAll) | The matchAll() method returns an iterator of all results matching a string against a regular expression, including capturing groups. |
| [RegExp::match](#RegExpmatch) | The match() method retrieves the result of matching a string against a regular expression. |
| [RegExp::__unserialize](#RegExp__unserialize) | {@inheritdoc} |
| [RegExp::__serialize](#RegExp__serialize) | {@inheritdoc} |
| [RegExp::jsonSerialize](#RegExpjsonSerialize) | {@inheritdoc} |
| [RegExp::__toString](#RegExp__toString) | {@inheritdoc} |
| [RegExp::__debugInfo](#RegExp__debugInfo) |  |
| [**RegExpException**](#RegExpException) |  |
| [RegExpException::__construct](#RegExpException__construct) |  |
| [RegExpException::getRegExp](#RegExpExceptiongetRegExp) | Get the RegExp Object |
| [**ResolverException**](#ResolverException) |  |
| [ResolverException::notTwice](#ResolverExceptionnotTwice) |  |
| [ResolverException::invalidCallable](#ResolverExceptioninvalidCallable) |  |
| [**Set**](#Set) | The Set object lets you store unique values of any type, whether primitive values or object references. |
| [Set::create](#Setcreate) | Create a new Set |
| [Set::add](#Setadd) | The add() method appends a new element with a specified value to the end of a Set object. |
| [Set::clear](#Setclear) | The clear() method removes all elements from a Set object. |
| [Set::delete](#Setdelete) | The delete() method removes a specified value from a Set object, if it is in the set. |
| [Set::entries](#Setentries) | The entries() method returns a new Iterator object that contains an array of [value, value] for each element in the Set object, in insertion order. |
| [Set::has](#Sethas) | The has() method returns a boolean indicating whether an element with the specified value exists in a Set object or not. |
| [Set::isEmpty](#SetisEmpty) | Checks if set is empty |
| [Set::values](#Setvalues) | The values() method returns a new Iterator object that contains the values for each element in the Set object in insertion order. |
| [Set::count](#Setcount) | {@inheritdoc} |
| [Set::getIterator](#SetgetIterator) | {@inheritdoc} |
| [Set::jsonSerialize](#SetjsonSerialize) | {@inheritdoc} |
| [Set::__debugInfo](#Set__debugInfo) | {@inheritdoc} |
| [Set::__serialize](#Set__serialize) |  |
| [Set::__unserialize](#Set__unserialize) |  |
| [Set::__clone](#Set__clone) | {@inheritdoc} |
| [**SharedList**](#SharedList) | Simulates Many-To-Many relations found in database |
| [SharedList::create](#SharedListcreate) | Create a new SharedList |
| [SharedList::clear](#SharedListclear) |  |
| [SharedList::hasValue](#SharedListhasValue) | Checks if value exists in the set |
| [SharedList::has](#SharedListhas) | Checks if relationship exists between 2 values |
| [SharedList::add](#SharedListadd) | Add a relationship between 2 values |
| [SharedList::deleteValue](#SharedListdeleteValue) | Removes a value and all its relationships |
| [SharedList::delete](#SharedListdelete) | Removes a relationship between 2 values |
| [SharedList::get](#SharedListget) | Get value shared list |
| [SharedList::entries](#SharedListentries) | Iterates all values shared lists |
| [SharedList::count](#SharedListcount) |  |
| [SharedList::getIterator](#SharedListgetIterator) |  |
| [SharedList::jsonSerialize](#SharedListjsonSerialize) |  |
| [SharedList::__serialize](#SharedList__serialize) |  |
| [SharedList::__unserialize](#SharedList__unserialize) |  |
| [SharedList::__debugInfo](#SharedList__debugInfo) |  |
| [**SimpleArray**](#SimpleArray) | A base Collection |
| [SimpleArray::unshift](#SimpleArrayunshift) | Prepend one or more elements to the beginning of an array |
| [SimpleArray::push](#SimpleArraypush) | Appends one or more elements at the end of an array |
| [SimpleArray::shift](#SimpleArrayshift) | Shift an element off the beginning of array |
| [SimpleArray::pop](#SimpleArraypop) | Pop the element off the end of array |
| [SimpleArray::indexOf](#SimpleArrayindexOf) | Returns the value index |
| [**SimpleCacheLock**](#SimpleCacheLock) | Use SimpleCache to manage your locks |
| [SimpleCacheLock::__construct](#SimpleCacheLock__construct) |  |
| [SimpleCacheLock::forceRelease](#SimpleCacheLockforceRelease) | {@inheritdoc} |
| [**SimpleIterator**](#SimpleIterator) | The SimpleIterator can iterate everything in any order |
| [SimpleIterator::__construct](#SimpleIterator__construct) |  |
| [SimpleIterator::of](#SimpleIteratorof) | Create a new SimpleIterator |
| [SimpleIterator::ofStringable](#SimpleIteratorofStringable) | Creates a new SimpleIterator that iterates each characters |
| [SimpleIterator::ofList](#SimpleIteratorofList) | Creates an iterator from a list |
| [SimpleIterator::count](#SimpleIteratorcount) |  |
| [SimpleIterator::entries](#SimpleIteratorentries) | Iterates entries in sort order |
| [**SimpleObject**](#SimpleObject) | A base Collection |
| [SimpleObject::search](#SimpleObjectsearch) | Searches the array for a given value and returns the first corresponding key if successful |
| [SimpleObject::__get](#SimpleObject__get) | {@inheritdoc} |
| [SimpleObject::__set](#SimpleObject__set) | {@inheritdoc} |
| [SimpleObject::__unset](#SimpleObject__unset) | {@inheritdoc} |
| [SimpleObject::__isset](#SimpleObject__isset) | {@inheritdoc} |
| [**SimpleServiceProvider**](#SimpleServiceProvider) |  |
| [SimpleServiceProvider::__construct](#SimpleServiceProvider__construct) |  |
| [SimpleServiceProvider::provides](#SimpleServiceProviderprovides) | Get the services provided by the provider. |
| [SimpleServiceProvider::register](#SimpleServiceProviderregister) | Register the service into the container |
| [**Slice**](#Slice) |  |
| [Slice::create](#Slicecreate) | Creates a Slice instance |
| [Slice::of](#Sliceof) | Create a Slice instance using python slice notation |
| [Slice::isValid](#SliceisValid) | Checks if valid slice syntax |
| [Slice::__construct](#Slice__construct) |  |
| [Slice::getStart](#SlicegetStart) |  |
| [Slice::getStop](#SlicegetStop) |  |
| [Slice::getStep](#SlicegetStep) |  |
| [Slice::getIteratorFor](#SlicegetIteratorFor) |  |
| [Slice::getOffsetList](#SlicegetOffsetList) |  |
| [Slice::slice](#Sliceslice) | Returns a slice of an array like object |
| [Slice::join](#Slicejoin) | Returns a String of a slice |
| [Slice::__debugInfo](#Slice__debugInfo) |  |
| [Slice::__toString](#Slice__toString) |  |
| [**SQLiteLock**](#SQLiteLock) | A SQLite database to manage your locks |
| [SQLiteLock::__construct](#SQLiteLock__construct) |  |
| [SQLiteLock::forceRelease](#SQLiteLockforceRelease) | {@inheritdoc} |
| [**StackableContainer**](#StackableContainer) |  |
| [StackableContainer::__construct](#StackableContainer__construct) |  |
| [StackableContainer::hasContainer](#StackableContainerhasContainer) | Check if container already stacked |
| [StackableContainer::addContainer](#StackableContaineraddContainer) | Stacks a new Container on top |
| [StackableContainer::get](#StackableContainerget) | {@inheritdoc} |
| [StackableContainer::has](#StackableContainerhas) | {@inheritdoc} |
| [**State**](#State) | Basic Enum Class Support (Polyfill)Adds the ability to class constants to work as php 8.1 backed enums cases |
| [**StopWatch**](#StopWatch) |  |
| [StopWatch::startTask](#StopWatchstartTask) | Starts a callable and returns result time |
| [StopWatch::startTaskWithStartTime](#StopWatchstartTaskWithStartTime) |  |
| [StopWatch::__construct](#StopWatch__construct) |  |
| [StopWatch::getTask](#StopWatchgetTask) |  |
| [StopWatch::executeTask](#StopWatchexecuteTask) |  |
| [StopWatch::start](#StopWatchstart) | Starts the clock |
| [StopWatch::resume](#StopWatchresume) | Resumes the clock (only if paused) |
| [StopWatch::reset](#StopWatchreset) | Resets the clock |
| [StopWatch::pause](#StopWatchpause) | Pauses the clock |
| [StopWatch::stop](#StopWatchstop) | Stops the clock |
| [StopWatch::read](#StopWatchread) | Reads the clock |
| [StopWatch::getLaps](#StopWatchgetLaps) |  |
| [StopWatch::lap](#StopWatchlap) | Adds a lap time |
| [StopWatch::isStarted](#StopWatchisStarted) |  |
| [StopWatch::isPaused](#StopWatchisPaused) |  |
| [StopWatch::isStopped](#StopWatchisStopped) |  |
| [**Text**](#Text) | Transform a scalar to its stringable representation |
| [Text::create](#Textcreate) | Create new Text |
| [Text::of](#Textof) | Create new Text |
| [Text::ofSegments](#TextofSegments) | Create multiple segments of Text |
| [Text::__construct](#Text__construct) |  |
| [Text::copy](#Textcopy) | Get a Text Copy |
| [Text::indexOf](#TextindexOf) | The indexOf() method, given one argument: a substring/regex to search for, searches the entire calling string, and returns the index of the first occurrence of the specified substring |
| [Text::search](#Textsearch) | Alias of indexOf |
| [Text::lastIndexOf](#TextlastIndexOf) | The lastIndexOf() method, given one argument: a substring/regex to search for, searches the entire calling string, and returns the index of the last occurrence of the specified substring. |
| [Text::at](#Textat) | The at() method takes an integer value and returns the character located at the specified offset |
| [Text::concat](#Textconcat) | The concat() method concatenates the string arguments to the current Text |
| [Text::toLowerCase](#TexttoLowerCase) | Converts Text to lower case |
| [Text::toUpperCase](#TexttoUpperCase) | Converts Text to upper case |
| [Text::endsWith](#TextendsWith) | The endsWith() method determines whether a string ends with the characters of a specified string, returning true or false as appropriate. |
| [Text::startsWith](#TextstartsWith) | The startsWith() method determines whether a string begins with the characters of a specified string, returning true or false as appropriate. |
| [Text::contains](#Textcontains) | The includes() method performs a search to determine whether one string may be found within another string/regex, returning true or false as appropriate. |
| [Text::containsAll](#TextcontainsAll) | Determine if a given string contains all needles |
| [Text::includes](#Textincludes) | The includes() method performs a case-sensitive search to determine whether one string may be found within another string, returning true or false as appropriate. |
| [Text::match](#Textmatch) | The match() method retrieves the result of matching a string against a regular expression. |
| [Text::matchAll](#TextmatchAll) | The matchAll() method returns an iterator of all results matching a string against a regular expression, including capturing groups. |
| [Text::padStart](#TextpadStart) | Pad the left side of a string with another. |
| [Text::padEnd](#TextpadEnd) | Pad the right side of a string with another. |
| [Text::pad](#Textpad) | Pad on both sides of a string with another. |
| [Text::repeat](#Textrepeat) | The repeat() method constructs and returns a new string which contains the specified number of copies of the string on which it was called,concatenated together. |
| [Text::replace](#Textreplace) | Replace the first occurrence of a given value in the string. |
| [Text::replaceAll](#TextreplaceAll) |  |
| [Text::substring](#Textsubstring) | The substring() method returns the part of the string between the start and end indexes, or to the end of the string. |
| [Text::ltrim](#Textltrim) | Left Trim the string of the given characters. |
| [Text::trimStart](#TexttrimStart) | Alias of ltrim |
| [Text::rtrim](#Textrtrim) | Right Trim the string of the given characters. |
| [Text::trimEnd](#TexttrimEnd) | Alias of rtrim |
| [Text::trim](#Texttrim) | Trim the string of the given characters. |
| [Text::capitalize](#Textcapitalize) | Return a copy of the string with its first character capitalized and the rest lowercased. |
| [Text::center](#Textcenter) | Return centered in a string of length width. Padding is done using the specified fillchar (default is an ASCII space). |
| [Text::expandtabs](#Textexpandtabs) | Return a copy of the string where all tab characters are replaced by one or more spaces |
| [Text::find](#Textfind) | Return the lowest index in the string where substring sub is found within the slice s[start:end]. |
| [Text::format](#Textformat) | Perform a string formatting operation. The string on which this method is called can contain literal text or replacement fields delimited by braces {}. |
| [Text::index](#Textindex) | Like find(), but raise ValueError when the substring is not found. |
| [Text::isalnum](#Textisalnum) | Return True if all characters in the string are alphanumeric and there is at least one character, False otherwise |
| [Text::isalpha](#Textisalpha) | Return True if all characters in the string are alphabetic and there is at least one character, False otherwise. |
| [Text::isdecimal](#Textisdecimal) | Return True if all characters in the string are decimal characters and there is at least one character, False otherwise |
| [Text::isdigit](#Textisdigit) | Return True if all characters in the string are digits and there is at least one character, False otherwise. |
| [Text::islower](#Textislower) | Return True if all cased characters in the string are lowercase and there is at least one cased character, False otherwise. |
| [Text::isnumeric](#Textisnumeric) | Finds whether a variable is a number or a numeric string |
| [Text::istitle](#Textistitle) | Return True if the string is a titlecased string and there is at least one character,for example uppercase characters may only follow uncased characters and lowercase characters only cased ones. |
| [Text::isspace](#Textisspace) | Return True if there are only whitespace characters in the string and there is at least one character, False otherwise. |
| [Text::isprintable](#Textisprintable) | Return True if all characters in the string are printable or the string is empty, False otherwise. |
| [Text::ispunct](#Textispunct) | Checks if all of the characters in the provided Text,  are punctuation character. |
| [Text::iscontrol](#Textiscontrol) | Checks if all characters in Text are control characters |
| [Text::isupper](#Textisupper) | Return True if all characters in the string are uppercase and there is at least one lowercase character, False otherwise. |
| [Text::join](#Textjoin) | Return a string which is the concatenation of the strings in iterable. |
| [Text::lower](#Textlower) | Return a copy of the string with all characters converted to lowercase. |
| [Text::lstrip](#Textlstrip) | Return a copy of the string with leading characters removed. |
| [Text::partition](#Textpartition) | Split the string at the first occurrence of sep,and return a 3-tuple containing the part before the separator, the separator itself, and the part after the separator. |
| [Text::removeprefix](#Textremoveprefix) | If the string starts with the prefix string,return string[len(prefix):]. Otherwise, return a copy of the original string: |
| [Text::removeSuffix](#TextremoveSuffix) | If the string ends with the suffix string and that suffix is not empty, return string[:-len(suffix)]. |
| [Text::reverse](#Textreverse) | Reverse the string |
| [Text::rfind](#Textrfind) | Return the highest index in the string where substring sub is found, such that sub is contained within s[start:end]. |
| [Text::rindex](#Textrindex) | Like rfind() but raises ValueError when the substring sub is not found. |
| [Text::rpartition](#Textrpartition) | Split the string at the last occurrence of sep,and return a 3-tuple containing the part before the separator, the separator itself, and the part after the separator. |
| [Text::rstrip](#Textrstrip) | Return a copy of the string with trailing characters removed. |
| [Text::strip](#Textstrip) | Return a copy of the string with the leading and trailing characters removed. |
| [Text::swapcase](#Textswapcase) | Return a copy of the string with uppercase characters converted to lowercase and vice versa. |
| [Text::slice](#Textslice) | The slice() method extracts a section of a string and returns it as a new string |
| [Text::title](#Texttitle) | Return a titlecased version of the string where words start with an uppercase character and the remaining characters are lowercase. |
| [Text::upper](#Textupper) | Return a copy of the string with all the cased characters converted to uppercase |
| [Text::split](#Textsplit) | Return a list of the words in the string, using sep as the delimiter string. |
| [Text::rsplit](#Textrsplit) | Return a list of the words in the string, using sep as the delimiter string. |
| [Text::splitlines](#Textsplitlines) | Return a list of the lines in the string, breaking at line boundaries. |
| [Text::sprintf](#Textsprintf) | Use sprintf to format string |
| [Text::ucfirst](#Textucfirst) | Use ucfirst on the string |
| [Text::lcfirst](#Textlcfirst) | Use lcfirst on the string |
| [Text::append](#Textappend) | Returns new Text with suffix added |
| [Text::prepend](#Textprepend) | Returns new Text with prefix added |
| [Text::isBase64](#TextisBase64) | Checks if Text is base 64 encoded |
| [Text::base64Encode](#Textbase64Encode) | Returns a base64 decoded Text |
| [Text::base64Decode](#Textbase64Decode) | Returns a base64 decoded Text |
| [Text::splitChars](#TextsplitChars) | Split the Text into multiple Text[] |
| [Text::countChars](#TextcountChars) | Count needle occurences inside Textif using a regex as needle the search will be case sensitive |
| [Text::isEquals](#TextisEquals) | Checks if Text is the same as the provided needle |
| [Text::ishexadecimal](#Textishexadecimal) | Checks if string is hexadecimal number |
| [Text::length](#Textlength) | Returns the length of the text |
| [Text::size](#Textsize) | Returns the byte size |
| [Text::offsetExists](#TextoffsetExists) |  |
| [Text::offsetGet](#TextoffsetGet) |  |
| [Text::offsetSet](#TextoffsetSet) |  |
| [Text::offsetUnset](#TextoffsetUnset) |  |
| [Text::count](#Textcount) |  |
| [Text::isEmpty](#TextisEmpty) |  |
| [Text::jsonSerialize](#TextjsonSerialize) |  |
| [Text::toString](#TexttoString) |  |
| [Text::__toString](#Text__toString) |  |
| [Text::__serialize](#Text__serialize) |  |
| [Text::__unserialize](#Text__unserialize) |  |
| [Text::__debugInfo](#Text__debugInfo) |  |
| [**Timer**](#Timer) |  |
| [Timer::getWatch](#TimergetWatch) | Get a watch |
| [Timer::read](#Timerread) | Reads the clock |
| [Timer::start](#Timerstart) | Starts the clock |
| [Timer::resume](#Timerresume) | Resumes the clock (only if paused) |
| [Timer::reset](#Timerreset) | Resets the clock |
| [Timer::resetAll](#TimerresetAll) | Resets all the clocks |
| [Timer::pause](#Timerpause) | Pauses the clock |
| [Timer::stop](#Timerstop) | Stops the clock |
| [Timer::getLaps](#TimergetLaps) |  |
| [Timer::lap](#Timerlap) | Adds a lap time |
| [**Tools**](#Tools) | Useful Functions to use in my projects |
| [Tools::safe_exec](#Toolssafe_exec) | Execute a callback and hides all php errors that can be thrownExceptions thrown inside the callback will be preserved |
| [Tools::errors_as_exceptions](#Toolserrors_as_exceptions) | Convenient Function used to convert php errors, warning, ... as Throwable |
| [Tools::suppress_errors](#Toolssuppress_errors) | Set error handler to empty closure (as of php 8.1 @ doesn&#039;t works anymore) |
| [Tools::normalize_path](#Toolsnormalize_path) | Normalize pathnames |
| [Tools::pushd](#Toolspushd) | Change the current active directoryAnd stores the last position, use popd() to return to previous directory |
| [Tools::popd](#Toolspopd) | Restore the last active directory changed by pushd |
| [Tools::each](#Toolseach) | Uses callback for each elements of the array and returns the value |
| [Tools::iterateAll](#ToolsiterateAll) | Iterate iterable |
| [Tools::filter](#Toolsfilter) | Filters elements of an iterable using a callback function |
| [Tools::search](#Toolssearch) | Searches an iterable until element is found |
| [Tools::map](#Toolsmap) | Same as the original except callback accepts more arguments and works with string keys |
| [Tools::some](#Toolssome) | Tests if at least one element in the iterable passes the test implemented by the provided function. |
| [Tools::every](#Toolsevery) | Tests if all elements in the iterable pass the test implemented by the provided function. |
| [Tools::pull](#Toolspull) | Get a value(s) from the array, and remove it. |
| [Tools::cloneArray](#ToolscloneArray) | Clone all objects of an array recursively |
| [Tools::iterableToArray](#ToolsiterableToArray) | Converts an iterable to an array recursivelyif the keys are not string the will be indexed |
| [Tools::concat](#Toolsconcat) | Concatenate multiple values into the iterable provided recursivelyIf a provided value is iterable it will be merged into the iterable(non numeric keys will be replaced if not iterable into the provided object) |
| [Tools::countValue](#ToolscountValue) | Count number of occurences of value |
| [Tools::isValidUrl](#ToolsisValidUrl) | Checks if is a valid url |
| [Tools::to_snake](#Toolsto_snake) | Convert CamelCased to camel_cased |
| [Tools::toCamelCase](#ToolstoCamelCase) | Convert snake_case to snakeCase |
| [Tools::millitime](#Toolsmillitime) | Return current Unix timestamp in milliseconds |
| [Tools::generate_uuid_v4](#Toolsgenerate_uuid_v4) | Generates a uuid V4 |
| [Tools::isAscii](#ToolsisAscii) | Returns whether this string consists entirely of ASCII characters |
| [Tools::isPrintableAscii](#ToolsisPrintableAscii) | Returns whether this string consists entirely of printable ASCII characters |
| [Tools::getFilesize](#ToolsgetFilesize) | Get Human Readable file size |
| [Tools::randomString](#ToolsrandomString) | Generate a more truly &quot;random&quot; alpha-numeric string. |
| [Tools::getWordSize](#ToolsgetWordSize) | Get the size of the longest word on a string |
| [Tools::splitString](#ToolssplitString) | Split the string at the given length without cutting words |
| [Tools::join](#Toolsjoin) | Joins iterable together using provided glue |
| [Tools::format](#Toolsformat) | Try to reproduce python format |
| [Tools::split](#Toolssplit) | Split a stringable using provided separator |
| [Tools::getExecutionTime](#ToolsgetExecutionTime) | Get script execution time |
| [Tools::pause](#Toolspause) | Pauses script execution for a given amount of timecombines sleep or usleep |
| [Tools::msleep](#Toolsmsleep) | Pauses script execution for a given amount of milliseconds |
| [Tools::implements_class](#Toolsimplements_class) | Get class implementing given parent class from the loaded classes |
| [Tools::getClassConstants](#ToolsgetClassConstants) | Get Constants defined in a class |
| [Tools::callPrivateMethod](#ToolscallPrivateMethod) | Call a method within an object ignoring its status |
| [**TypeCheck**](#TypeCheck) | Checks for mixed union/intersection types |
| [TypeCheck::assertType](#TypeCheckassertType) | Check the given value against the supplied types and throw TypeError if not valid |
| [TypeCheck::assertTypeMethod](#TypeCheckassertTypeMethod) | Check the given value against the supplied types and throw TypeError if not valid |
| [TypeCheck::checkType](#TypeCheckcheckType) | Can check a mix of intersection and union |
| [**Units**](#Units) | Basic Enum Class Support (Polyfill)Adds the ability to class constants to work as php 8.1 backed enums cases |
| [Units::getStep](#UnitsgetStep) |  |
| [Units::getPlural](#UnitsgetPlural) |  |
| [Units::getSingular](#UnitsgetSingular) |  |
| [**WatchFactory**](#WatchFactory) |  |
| [WatchFactory::__construct](#WatchFactory__construct) |  |
| [WatchFactory::getWatch](#WatchFactorygetWatch) | Get a watch |
| [WatchFactory::read](#WatchFactoryread) | Reads the clock |
| [WatchFactory::start](#WatchFactorystart) | Starts the clock |
| [WatchFactory::resume](#WatchFactoryresume) | Resumes the clock (only if paused) |
| [WatchFactory::reset](#WatchFactoryreset) | Resets the clock |
| [WatchFactory::resetAll](#WatchFactoryresetAll) | Resets all the clocks |
| [WatchFactory::pause](#WatchFactorypause) | Pauses the clock |
| [WatchFactory::stop](#WatchFactorystop) | Stops the clock |
| [WatchFactory::getLaps](#WatchFactorygetLaps) |  |
| [WatchFactory::lap](#WatchFactorylap) | Adds a lap time |

## CacheLock

Use a cache pool to manage your locks



* Full name: \NGSOFT\Lock\CacheLock
* Parent class: \NGSOFT\Lock\CacheLockAbstract


### CacheLock::__construct



```php
CacheLock::__construct( \Psr\Cache\CacheItemPoolInterface cache, string|\Stringable name, int|float seconds, string|\Stringable owner = '', bool autoRelease = true ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `cache` | **\Psr\Cache\CacheItemPoolInterface** |  |
| `name` | **string\|\Stringable** |  |
| `seconds` | **int\|float** |  |
| `owner` | **string\|\Stringable** |  |
| `autoRelease` | **bool** |  |


**Return Value:**





---
### CacheLock::forceRelease

{@inheritdoc}

```php
CacheLock::forceRelease(  ): void
```





**Return Value:**





---
## CharMap

A Multibyte/byte string convertion Map



* Full name: \NGSOFT\Tools\CharMap
* This class implements: \Stringable, \Countable


### CharMap::create

Create a new CharMap

```php
CharMap::create( string string ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `string` | **string** |  |


**Return Value:**





---
### CharMap::getCharOffset

Get character offset from byte offset
Returns -1 on failure

```php
CharMap::getCharOffset( string string, int byte ): int
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `string` | **string** |  |
| `byte` | **int** |  |


**Return Value:**





---
### CharMap::getByteOffset

Get byte offset from character Offset
returns -1 on failure

```php
CharMap::getByteOffset( string string, int char ): int
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `string` | **string** |  |
| `char` | **int** |  |


**Return Value:**





---
### CharMap::__construct

Create a new CharMap

```php
CharMap::__construct( string string ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `string` | **string** |  |


**Return Value:**





---
### CharMap::convertByteOffset

Get Character Offset from Byte Offset

```php
CharMap::convertByteOffset( int byte ): int
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `byte` | **int** |  |


**Return Value:**





---
### CharMap::convertCharacterOffset

Get Byte offset from Character Offset

```php
CharMap::convertCharacterOffset( int char ): int
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `char` | **int** |  |


**Return Value:**





---
### CharMap::getLength

Get number of characters

```php
CharMap::getLength(  ): int
```





**Return Value:**





---
### CharMap::getSize

Get number of bytes

```php
CharMap::getSize(  ): int
```





**Return Value:**





---
### CharMap::count



```php
CharMap::count(  ): int
```





**Return Value:**





---
### CharMap::isEmpty



```php
CharMap::isEmpty(  ): bool
```





**Return Value:**





---
### CharMap::toString



```php
CharMap::toString(  ): string
```





**Return Value:**





---
### CharMap::__toString



```php
CharMap::__toString(  ): string
```





**Return Value:**





---
### CharMap::__unserialize



```php
CharMap::__unserialize( array data ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `data` | **array** |  |


**Return Value:**





---
### CharMap::__serialize



```php
CharMap::__serialize(  ): array
```





**Return Value:**





---
## CircularDependencyException





* Full name: \NGSOFT\Container\Exceptions\CircularDependencyException
* Parent class: \NGSOFT\Container\Exceptions\ContainerError


## Container





* Full name: \NGSOFT\Container\Container
* This class implements: \NGSOFT\Container\ContainerInterface


### Container::__construct



```php
Container::__construct( iterable definitions = [] ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `definitions` | **iterable** |  |


**Return Value:**





---
### Container::alias

Alias an entry to a different name

```php
Container::alias( string|array alias, string id ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `alias` | **string\|array** |  |
| `id` | **string** |  |


**Return Value:**





---
### Container::has

{@inheritdoc}

```php
Container::has( string id ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | **string** |  |


**Return Value:**





---
### Container::get

{@inheritdoc}

```php
Container::get( string id ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | **string** |  |


**Return Value:**





---
### Container::make

Resolves an entry by its name. If given a class name, it will return a fresh instance of that class.

```php
Container::make( string id, array parameters = [] ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | **string** |  |
| `parameters` | **array** |  |


**Return Value:**





---
### Container::call

Call the given function using the given parameters.

```php
Container::call( object|array|string callable, array parameters = [] ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `callable` | **object\|array\|string** |  |
| `parameters` | **array** |  |


**Return Value:**





---
### Container::register

Register a service

```php
Container::register( \NGSOFT\Container\ServiceProvider service ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `service` | **\NGSOFT\Container\ServiceProvider** |  |


**Return Value:**





---
### Container::set

Add a definition to the container

```php
Container::set( string id, mixed value ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | **string** |  |
| `value` | **mixed** |  |


**Return Value:**





---
### Container::setMany

Adds multiple definitions

```php
Container::setMany( iterable definitions ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `definitions` | **iterable** |  |


**Return Value:**





---
### Container::addContainerResolver

Adds an handler to manage entry resolution (afyer params have been resolved)

```php
Container::addContainerResolver( \NGSOFT\Container\Resolvers\ContainerResolver resolver, ?int priority = null ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `resolver` | **\NGSOFT\Container\Resolvers\ContainerResolver** |  |
| `priority` | **?int** |  |


**Return Value:**





---
### Container::__debugInfo



```php
Container::__debugInfo(  ): mixed
```





**Return Value:**





---
## Container





* Full name: \NGSOFT\Facades\Container
* Parent class: \NGSOFT\Facades\Facade


### Container::alias

Alias an entry to a different name

```php
Container::alias( array|string alias, string id ): void
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `alias` | **array\|string** |  |
| `id` | **string** |  |


**Return Value:**





---
### Container::has

Returns true if the container can return an entry for the given identifier.

```php
Container::has( string id ): bool
```

Returns false otherwise.

* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | **string** |  |


**Return Value:**





---
### Container::get

Finds an entry of the container by its identifier and returns it.

```php
Container::get( string id ): mixed
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | **string** |  |


**Return Value:**





---
### Container::make

Resolves an entry by its name. If given a class name, it will return a fresh instance of that class.

```php
Container::make( string id, array parameters = [] ): mixed
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | **string** |  |
| `parameters` | **array** |  |


**Return Value:**





---
### Container::call

Call the given function using the given parameters.

```php
Container::call( object|array|string callable, array parameters = [] ): mixed
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `callable` | **object\|array\|string** |  |
| `parameters` | **array** |  |


**Return Value:**





---
### Container::register

Register a service

```php
Container::register( \NGSOFT\Container\ServiceProvider service ): void
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `service` | **\NGSOFT\Container\ServiceProvider** |  |


**Return Value:**





---
### Container::set

Add a definition to the container

```php
Container::set( string id, mixed value ): void
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | **string** |  |
| `value` | **mixed** |  |


**Return Value:**





---
### Container::setMany

Adds multiple definitions

```php
Container::setMany( iterable definitions ): void
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `definitions` | **iterable** |  |


**Return Value:**





---
### Container::addContainerResolver

Adds an handler to manage entry resolution (after params have been resolved)

```php
Container::addContainerResolver( \NGSOFT\Container\Resolvers\ContainerResolver resolver, ?int priority = null ): void
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `resolver` | **\NGSOFT\Container\Resolvers\ContainerResolver** |  |
| `priority` | **?int** |  |


**Return Value:**





---
## ContainerError





* Full name: \NGSOFT\Container\Exceptions\ContainerError
* Parent class: 
* This class implements: \Psr\Container\ContainerExceptionInterface


## Directory

Manages a directory



* Full name: \NGSOFT\Filesystem\Directory
* Parent class: \NGSOFT\Filesystem\Filesystem
* This class implements: \IteratorAggregate


### Directory::scanFiles

Scan files in a directory

```php
Directory::scanFiles( string dirname, bool recursive = false ): \NGSOFT\Filesystem\FileList
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `dirname` | **string** |  |
| `recursive` | **bool** |  |


**Return Value:**





---
### Directory::scanFilesArray



```php
Directory::scanFilesArray( string dirname, bool recursive = false ): array
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `dirname` | **string** |  |
| `recursive` | **bool** |  |


**Return Value:**





---
### Directory::cwd



```php
Directory::cwd(  ): static
```



* This method is **static**.

**Return Value:**





---
### Directory::pushd

Change the current active directory and stores the last position

```php
Directory::pushd( string|self directory ): static|false
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `directory` | **string\|self** |  |


**Return Value:**





---
### Directory::popd

Restore the last active directory position and returns it

```php
Directory::popd(  ): static|false
```



* This method is **static**.

**Return Value:**





---
### Directory::__construct



```php
Directory::__construct( string path = '' ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `path` | **string** |  |


**Return Value:**





---
### Directory::copy

Copy Directory to another location

```php
Directory::copy( string|self target, ?bool &success = null ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `target` | **string\|self** | new directory |
| `success` | **?bool** | True if the operation succeeded |


**Return Value:**

a Directory instance for the target



---
### Directory::delete

Recursively delete a directory.

```php
Directory::delete(  ): bool
```





**Return Value:**





---
### Directory::exists

Checks if directory exists

```php
Directory::exists(  ): bool
```





**Return Value:**





---
### Directory::isEmpty

Checks if no files

```php
Directory::isEmpty(  ): bool
```





**Return Value:**





---
### Directory::mkdir

Create dir

```php
Directory::mkdir( int mode = 0777, bool recursive = true ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `mode` | **int** |  |
| `recursive` | **bool** |  |


**Return Value:**





---
### Directory::rmdir

Remove dir

```php
Directory::rmdir(  ): bool
```





**Return Value:**





---
### Directory::chdir

Change dir

```php
Directory::chdir(  ): bool
```





**Return Value:**





---
### Directory::isCurrentWorkingDir

Checks if is current active dir

```php
Directory::isCurrentWorkingDir(  ): bool
```





**Return Value:**





---
### Directory::search

Search for a file recursively using regex, glob or check if filename contains $query

```php
Directory::search( string pattern ): \NGSOFT\Filesystem\FileList
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pattern` | **string** |  |


**Return Value:**





---
### Directory::glob

Executes a glob search inside the directory

```php
Directory::glob( string pattern, int flags ): \NGSOFT\Filesystem\FileList
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pattern` | **string** |  |
| `flags` | **int** |  |


**Return Value:**





---
### Directory::files

List files inside directory

```php
Directory::files( string|array extensions = [], bool hidden = false ): \NGSOFT\Filesystem\FileList
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `extensions` | **string\|array** |  |
| `hidden` | **bool** |  |


**Return Value:**





---
### Directory::allFiles

List files recursively

```php
Directory::allFiles( string|array extensions = [], bool hidden = false ): \NGSOFT\Filesystem\FileList
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `extensions` | **string\|array** |  |
| `hidden` | **bool** |  |


**Return Value:**





---
### Directory::directories

List directories

```php
Directory::directories( bool recursive = false ): \NGSOFT\Filesystem\FileList
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `recursive` | **bool** |  |


**Return Value:**





---
### Directory::getFile

Access a file in that directory

```php
Directory::getFile( string target ): \NGSOFT\Filesystem\File|\NGSOFT\Filesystem\Directory
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `target` | **string** |  |


**Return Value:**





---
### Directory::getIterator



```php
Directory::getIterator(  ): \Traversable
```





**Return Value:**





---
## EnumUtils





* Full name: \NGSOFT\Enums\EnumUtils


### EnumUtils::generateEnumClassPhpDoc

Generates Doc Comment for magic static methods

```php
EnumUtils::generateEnumClassPhpDoc( string className ): string
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `className` | **string** |  |


**Return Value:**





---
### EnumUtils::addPhpDocToEnumClass

Auto Generates static methods doc blocks for enums

```php
EnumUtils::addPhpDocToEnumClass( string className ): bool
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `className` | **string** |  |


**Return Value:**





---
## FacadeUtils





* Full name: \NGSOFT\Tools\Utils\FacadeUtils


### FacadeUtils::getClassDocBlocks



```php
FacadeUtils::getClassDocBlocks( object|string instance, bool static = true ): array
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `instance` | **object\|string** |  |
| `static` | **bool** |  |


**Return Value:**





---
### FacadeUtils::createDocBlock



```php
FacadeUtils::createDocBlock( string facade ): string
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `facade` | **string** |  |


**Return Value:**





---
### FacadeUtils::createMethodsForInstance



```php
FacadeUtils::createMethodsForInstance( object|string instance, string facade = null ): string
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `instance` | **object\|string** |  |
| `facade` | **string** |  |


**Return Value:**





---
### FacadeUtils::createMethods



```php
FacadeUtils::createMethods( string facade ): mixed
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `facade` | **string** |  |


**Return Value:**





---
### FacadeUtils::createFacadeCode



```php
FacadeUtils::createFacadeCode( object instance, ?string name = null, ?string accessor = null ): mixed
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `instance` | **object** |  |
| `name` | **?string** |  |
| `accessor` | **?string** |  |


**Return Value:**





---
## File

Manages a File



* Full name: \NGSOFT\Filesystem\File
* Parent class: \NGSOFT\Filesystem\Filesystem
* This class implements: \IteratorAggregate


### File::__construct



```php
File::__construct( string path ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `path` | **string** |  |


**Return Value:**





---
### File::__destruct



```php
File::__destruct(  ): mixed
```





**Return Value:**





---
### File::getDirectory

Get file directory

```php
File::getDirectory(  ): \NGSOFT\Filesystem\Directory
```





**Return Value:**





---
### File::exists

Checks if file exists and is regular file

```php
File::exists(  ): bool
```





**Return Value:**





---
### File::isModified

Check if crc checksum has changed

```php
File::isModified(  ): bool
```





**Return Value:**





---
### File::unlink

Deletes the file

```php
File::unlink(  ): bool
```





**Return Value:**





---
### File::copy

Copy File

```php
File::copy( string|self target, ?bool &success = null ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `target` | **string\|self** | new file |
| `success` | **?bool** | True if the operation succeeded |


**Return Value:**

a File instance for the target



---
### File::delete

Delete the file

```php
File::delete(  ): bool
```





**Return Value:**





---
### File::require

Includes file as php file

```php
File::require( array data = [], bool once = false ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `data` | **array** | data to extract |
| `once` | **bool** | require_once |


**Return Value:**





---
### File::name

Get file name without extension

```php
File::name(  ): string
```





**Return Value:**





---
### File::extension

Get the last file extension

```php
File::extension(  ): string
```





**Return Value:**





---
### File::hash

Get CRC32 Checksum

```php
File::hash(  ): string|null
```





**Return Value:**





---
### File::touch

Sets access and modification time of file

```php
File::touch( int|null mtime = null, int|null atime = null ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `mtime` | **int\|null** |  |
| `atime` | **int\|null** |  |


**Return Value:**





---
### File::getContents

Loads file as an Iterator

```php
File::getContents(  ): \NGSOFT\Filesystem\FileContents
```





**Return Value:**





---
### File::createContents

Creates file contents

```php
File::createContents(  ): \NGSOFT\Filesystem\FileContents
```





**Return Value:**





---
### File::read

Loads file

```php
File::read(  ): string
```





**Return Value:**





---
### File::readAsArray

Read file as array of lines

```php
File::readAsArray(  ): string[]
```





**Return Value:**





---
### File::readJson

Decodes json file

```php
File::readJson(  ): mixed
```





**Return Value:**





---
### File::write

Save File

```php
File::write( string|string[]|\Stringable|\Stringable[] contents ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `contents` | **string\|string[]\|\Stringable\|\Stringable[]** |  |


**Return Value:**





---
### File::writeJson

Dumps data to json

```php
File::writeJson( mixed data, int flags = JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `data` | **mixed** |  |
| `flags` | **int** |  |


**Return Value:**





---
### File::getIterator



```php
File::getIterator(  ): \Traversable
```





**Return Value:**





---
### File::lock

Locks file access on concurrent requests

```php
File::lock( int|float seconds, string owner = '' ): \NGSOFT\Lock\FileSystemLock
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `seconds` | **int\|float** |  |
| `owner` | **string** |  |


**Return Value:**





---
### File::__debugInfo



```php
File::__debugInfo(  ): array
```





**Return Value:**





---
## FileContents





* Full name: \NGSOFT\Filesystem\FileContents
* This class implements: \IteratorAggregate, \ArrayAccess, \Countable, \Stringable, \JsonSerializable


### FileContents::__construct



```php
FileContents::__construct( \NGSOFT\Filesystem\File file, array lines = [], bool loaded = false ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `file` | **\NGSOFT\Filesystem\File** |  |
| `lines` | **array** |  |
| `loaded` | **bool** |  |


**Return Value:**





---
### FileContents::refresh

Reorganize lines

```php
FileContents::refresh(  ): void
```





**Return Value:**





---
### FileContents::reload

Reloads file contents

```php
FileContents::reload(  ): void
```





**Return Value:**





---
### FileContents::clear

Clears the contents

```php
FileContents::clear(  ): void
```





**Return Value:**





---
### FileContents::map

Run the callable with all the lines and replaces the contents with the return value

```php
FileContents::map( callable callable ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `callable` | **callable** |  |


**Return Value:**





---
### FileContents::filter

Run a callable for all the line and removes line that does not pass the test

```php
FileContents::filter( callable callable ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `callable` | **callable** |  |


**Return Value:**





---
### FileContents::save

Save file contents

```php
FileContents::save(  ): bool
```





**Return Value:**





---
### FileContents::readLine

Reads a line

```php
FileContents::readLine( int offset ): string|null
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **int** |  |


**Return Value:**





---
### FileContents::write

Replaces the entire contents

```php
FileContents::write( string|iterable lines ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `lines` | **string\|iterable** |  |


**Return Value:**





---
### FileContents::writeLine

replaces / adds a line

```php
FileContents::writeLine( string value, int|null offset = null ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **string** |  |
| `offset` | **int\|null** |  |


**Return Value:**





---
### FileContents::insertLine

Insert a line
if no offset defined will add to the begining of the file, if out of range will be added to the end of the file

```php
FileContents::insertLine( string value, int|null offset = null ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **string** |  |
| `offset` | **int\|null** |  |


**Return Value:**





---
### FileContents::removeLine

Delete a line, also reorganize lines

```php
FileContents::removeLine( int offset ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **int** |  |


**Return Value:**





---
### FileContents::offsetExists

{@inheritdoc}

```php
FileContents::offsetExists( mixed offset ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |


**Return Value:**





---
### FileContents::offsetGet

{@inheritdoc}

```php
FileContents::offsetGet( mixed offset ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |


**Return Value:**





---
### FileContents::offsetSet

{@inheritdoc}

```php
FileContents::offsetSet( mixed offset, mixed value ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |
| `value` | **mixed** |  |


**Return Value:**





---
### FileContents::offsetUnset

{@inheritdoc}

```php
FileContents::offsetUnset( mixed offset ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |


**Return Value:**





---
### FileContents::isEmpty



```php
FileContents::isEmpty(  ): bool
```





**Return Value:**





---
### FileContents::count

{@inheritdoc}

```php
FileContents::count(  ): int
```





**Return Value:**





---
### FileContents::getIterator



```php
FileContents::getIterator(  ): \Traversable&lt;int,string&gt;
```





**Return Value:**





---
### FileContents::jsonSerialize

{@inheritdoc}

```php
FileContents::jsonSerialize(  ): mixed
```





**Return Value:**





---
### FileContents::__toString

{@inheritdoc}

```php
FileContents::__toString(  ): string
```





**Return Value:**





---
### FileContents::__serialize

{@inheritdoc}

```php
FileContents::__serialize(  ): array
```





**Return Value:**





---
### FileContents::__unserialize

{@inheritdoc}

```php
FileContents::__unserialize( array data ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `data` | **array** |  |


**Return Value:**





---
### FileContents::__debugInfo



```php
FileContents::__debugInfo(  ): array
```





**Return Value:**





---
## FileFactory





* Full name: \NGSOFT\Filesystem\FileFactory


### FileFactory::getFile

Get a File instance

```php
FileFactory::getFile( string filename ): \NGSOFT\Filesystem\File
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `filename` | **string** |  |


**Return Value:**





---
### FileFactory::getDirectory

Get a Directory instance

```php
FileFactory::getDirectory( string dirname ): \NGSOFT\Filesystem\Directory
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `dirname` | **string** |  |


**Return Value:**





---
### FileFactory::getFileContents

Get File Contents

```php
FileFactory::getFileContents( string filename ): \NGSOFT\Filesystem\FileContents
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `filename` | **string** |  |


**Return Value:**





---
## FileList

File list Iterator



* Full name: \NGSOFT\Filesystem\FileList
* This class implements: \IteratorAggregate, \Countable


### FileList::create



```php
FileList::create( array files = [] ): mixed
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `files` | **array** |  |


**Return Value:**





---
### FileList::append

Adds a file to the list

```php
FileList::append( string|iterable|\NGSOFT\Filesystem\Filesystem files ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `files` | **string\|iterable\|\NGSOFT\Filesystem\Filesystem** |  |


**Return Value:**





---
### FileList::files

Returns only files

```php
FileList::files(  ): static
```





**Return Value:**





---
### FileList::directories

Returns only directories

```php
FileList::directories(  ): static
```





**Return Value:**





---
### FileList::filter

Filter results using callable

```php
FileList::filter( callable callable ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `callable` | **callable** |  |


**Return Value:**





---
### FileList::toArray

Returns files realpaths

```php
FileList::toArray(  ): array
```





**Return Value:**





---
### FileList::isEmpty



```php
FileList::isEmpty(  ): bool
```





**Return Value:**





---
### FileList::count



```php
FileList::count(  ): int
```





**Return Value:**





---
### FileList::getIterator



```php
FileList::getIterator(  ): \Traversable&lt;string,\NGSOFT\Filesystem\File|\NGSOFT\Filesystem\Directory&gt;
```





**Return Value:**





---
### FileList::keys



```php
FileList::keys(  ): string[]
```





**Return Value:**





---
### FileList::values



```php
FileList::values(  ): \NGSOFT\Filesystem\File[]|\NGSOFT\Filesystem\Directory[]
```





**Return Value:**





---
### FileList::__serialize



```php
FileList::__serialize(  ): array
```





**Return Value:**





---
### FileList::__unserialize



```php
FileList::__unserialize( array data ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `data` | **array** |  |


**Return Value:**





---
### FileList::__debugInfo



```php
FileList::__debugInfo(  ): array
```





**Return Value:**





---
## FileLock

Uses php files to create locks



* Full name: \NGSOFT\Lock\FileLock
* Parent class: \NGSOFT\Lock\BaseLockStore


### FileLock::__construct



```php
FileLock::__construct( string|\Stringable name, int|float seconds, string|\Stringable owner = '', bool autoRelease = true, string rootpath = '', string prefix = '@flocks' ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | **string\|\Stringable** |  |
| `seconds` | **int\|float** |  |
| `owner` | **string\|\Stringable** |  |
| `autoRelease` | **bool** |  |
| `rootpath` | **string** | where to put the locks |
| `prefix` | **string** | subdirectory to $rootpath |


**Return Value:**





---
### FileLock::forceRelease

{@inheritdoc}

```php
FileLock::forceRelease(  ): void
```





**Return Value:**





---
## FileSystem





* Full name: \NGSOFT\Facades\FileSystem
* Parent class: \NGSOFT\Facades\Facade


### FileSystem::getFile

Get a File instance

```php
FileSystem::getFile( string filename ): \NGSOFT\Filesystem\File
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `filename` | **string** |  |


**Return Value:**





---
### FileSystem::getDirectory

Get a Directory instance

```php
FileSystem::getDirectory( string dirname ): \NGSOFT\Filesystem\Directory
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `dirname` | **string** |  |


**Return Value:**





---
### FileSystem::getFileContents

Get File Contents

```php
FileSystem::getFileContents( string filename ): \NGSOFT\Filesystem\FileContents
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `filename` | **string** |  |


**Return Value:**





---
## FileSystemLock

Creates a lock file with the same filename and directory as provided file



* Full name: \NGSOFT\Lock\FileSystemLock
* Parent class: \NGSOFT\Lock\BaseLockStore


### FileSystemLock::__construct



```php
FileSystemLock::__construct( \NGSOFT\Filesystem\File name, int|float seconds, string|\Stringable owner = '', bool autoRelease = true ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | **\NGSOFT\Filesystem\File** |  |
| `seconds` | **int\|float** |  |
| `owner` | **string\|\Stringable** |  |
| `autoRelease` | **bool** |  |


**Return Value:**





---
### FileSystemLock::forceRelease



```php
FileSystemLock::forceRelease(  ): void
```





**Return Value:**





---
## FixedArray

An array with fixed capacity
Uses LRU model (Last Recently Used gets removed first)
SplFixedArray only works with int offsets (not null or strings)



* Full name: \NGSOFT\DataStructure\FixedArray
* This class implements: \NGSOFT\DataStructure\ReversibleIterator, \ArrayAccess, \JsonSerializable, \Stringable


### FixedArray::create

Creates a new Fixed Array

```php
FixedArray::create( int size = self::DEFAULT_CAPACITY ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `size` | **int** |  |


**Return Value:**





---
### FixedArray::__construct



```php
FixedArray::__construct( int size = self::DEFAULT_CAPACITY ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `size` | **int** |  |


**Return Value:**





---
### FixedArray::clear



```php
FixedArray::clear(  ): void
```





**Return Value:**





---
### FixedArray::getSize

Gets the size of the array.

```php
FixedArray::getSize(  ): int
```





**Return Value:**





---
### FixedArray::setSize

Change the size of an array to the new size of size. If size is less than the current array size,
any values after the new size will be discarded.

```php
FixedArray::setSize( int size ): bool
```

If size is greater than the current array size, the array will be padded with null values.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `size` | **int** |  |


**Return Value:**





---
### FixedArray::count

{@inheritdoc}

```php
FixedArray::count(  ): int
```





**Return Value:**





---
### FixedArray::entries

Iterates entries in sort order

```php
FixedArray::entries( \NGSOFT\DataStructure\Sort sort = Sort::ASC ): iterable
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sort` | **\NGSOFT\DataStructure\Sort** |  |


**Return Value:**





---
### FixedArray::keys

Returns a new iterable with only the indexes

```php
FixedArray::keys( \NGSOFT\DataStructure\Sort sort = Sort::ASC ): iterable
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sort` | **\NGSOFT\DataStructure\Sort** |  |


**Return Value:**





---
### FixedArray::values

Returns a new iterable with only the values

```php
FixedArray::values( \NGSOFT\DataStructure\Sort sort = Sort::ASC ): iterable
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sort` | **\NGSOFT\DataStructure\Sort** |  |


**Return Value:**





---
### FixedArray::jsonSerialize

{@inheritdoc}

```php
FixedArray::jsonSerialize(  ): mixed
```





**Return Value:**





---
### FixedArray::offsetExists

{@inheritdoc}

```php
FixedArray::offsetExists( mixed offset ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |


**Return Value:**





---
### FixedArray::offsetGet

{@inheritdoc}

```php
FixedArray::offsetGet( mixed offset ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |


**Return Value:**





---
### FixedArray::offsetSet

{@inheritdoc}

```php
FixedArray::offsetSet( mixed offset, mixed value ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |
| `value` | **mixed** |  |


**Return Value:**





---
### FixedArray::offsetUnset

{@inheritdoc}

```php
FixedArray::offsetUnset( mixed offset ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |


**Return Value:**





---
### FixedArray::__debugInfo

{@inheritdoc}

```php
FixedArray::__debugInfo(  ): array
```





**Return Value:**





---
### FixedArray::__serialize

{@inheritdoc}

```php
FixedArray::__serialize(  ): array
```





**Return Value:**





---
### FixedArray::__unserialize

{@inheritdoc}

```php
FixedArray::__unserialize( array data ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `data` | **array** |  |


**Return Value:**





---
### FixedArray::__clone

{@inheritdoc}

```php
FixedArray::__clone(  ): void
```





**Return Value:**





---
## Inject





* Full name: \NGSOFT\Container\Attribute\Inject
* This class implements: \Stringable


### Inject::__construct



```php
Inject::__construct( string|array name = '' ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | **string\|array** |  |


**Return Value:**





---
### Inject::__toString



```php
Inject::__toString(  ): string
```





**Return Value:**





---
## InjectProperties

Scans for #[Inject] attribute on the loaded class properties



* Full name: \NGSOFT\Container\Resolvers\InjectProperties
* Parent class: \NGSOFT\Container\Resolvers\ContainerResolver


### InjectProperties::resolve

Resolves an entry from the container

```php
InjectProperties::resolve( mixed value ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **mixed** |  |


**Return Value:**





---
### InjectProperties::getDefaultPriority

Set the default priority

```php
InjectProperties::getDefaultPriority(  ): int
```





**Return Value:**





---
## InnerFacade





* Full name: \NGSOFT\Facades\Facade\InnerFacade
* Parent class: \NGSOFT\Facades\Facade


### InnerFacade::boot

Starts the container

```php
InnerFacade::boot( array definitions = [] ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `definitions` | **array** |  |


**Return Value:**





---
### InnerFacade::registerServiceProvider



```php
InnerFacade::registerServiceProvider( string accessor, \NGSOFT\Container\ServiceProvider provider ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `accessor` | **string** |  |
| `provider` | **\NGSOFT\Container\ServiceProvider** |  |


**Return Value:**





---
### InnerFacade::getResovedInstance



```php
InnerFacade::getResovedInstance( string name ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | **string** |  |


**Return Value:**





---
### InnerFacade::setResolvedInstance



```php
InnerFacade::setResolvedInstance( string name, object instance ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | **string** |  |
| `instance` | **object** |  |


**Return Value:**





---
### InnerFacade::getContainer



```php
InnerFacade::getContainer(  ): \NGSOFT\Container\ContainerInterface
```





**Return Value:**





---
### InnerFacade::setContainer



```php
InnerFacade::setContainer( \NGSOFT\Container\ContainerInterface container ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `container` | **\NGSOFT\Container\ContainerInterface** |  |


**Return Value:**





---
## JsonObject

A Json object that syncs data with a json file concurently



* Full name: \NGSOFT\DataStructure\JsonObject
* Parent class: \NGSOFT\DataStructure\SimpleObject


### JsonObject::fromJsonFile



```php
JsonObject::fromJsonFile( string filename, bool recursive = true ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `filename` | **string** |  |
| `recursive` | **bool** |  |


**Return Value:**





---
## Lock





* Full name: \NGSOFT\Facades\Lock
* Parent class: \NGSOFT\Facades\Facade


### Lock::createFileLock

Create a Php File Lock

```php
Lock::createFileLock( string name, int seconds, string owner = '', string rootpath = '' ): \NGSOFT\Lock\FileLock
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | **string** |  |
| `seconds` | **int** |  |
| `owner` | **string** |  |
| `rootpath` | **string** |  |


**Return Value:**





---
### Lock::createFileSystemLock



```php
Lock::createFileSystemLock( string|\NGSOFT\Filesystem\File file, int seconds, string owner = '' ): \NGSOFT\Lock\FileSystemLock
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `file` | **string\|\NGSOFT\Filesystem\File** |  |
| `seconds` | **int** |  |
| `owner` | **string** |  |


**Return Value:**





---
### Lock::createSQLiteLock

Create a SQLite Lock

```php
Lock::createSQLiteLock( string name, int seconds, string owner = '', string dbname = 'sqlocks.db3', string table = 'locks' ): \NGSOFT\Lock\SQLiteLock
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | **string** |  |
| `seconds` | **int** |  |
| `owner` | **string** |  |
| `dbname` | **string** |  |
| `table` | **string** |  |


**Return Value:**





---
### Lock::createNoLock

Create a NoLock

```php
Lock::createNoLock( string name, int seconds, string owner = '' ): \NGSOFT\Lock\NoLock
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | **string** |  |
| `seconds` | **int** |  |
| `owner` | **string** |  |


**Return Value:**





---
### Lock::createCacheLock

Create a lock using a PSR-6 Cache

```php
Lock::createCacheLock( \Psr\Cache\CacheItemPoolInterface cache, string name, int seconds, string owner = '' ): \NGSOFT\Lock\CacheLock
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `cache` | **\Psr\Cache\CacheItemPoolInterface** |  |
| `name` | **string** |  |
| `seconds` | **int** |  |
| `owner` | **string** |  |


**Return Value:**





---
### Lock::createSimpleCacheLock

Create a lock using a PSR-16 Cache

```php
Lock::createSimpleCacheLock( \Psr\SimpleCache\CacheInterface cache, string name, int seconds, string owner = '' ): \NGSOFT\Lock\SimpleCacheLock
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `cache` | **\Psr\SimpleCache\CacheInterface** |  |
| `name` | **string** |  |
| `seconds` | **int** |  |
| `owner` | **string** |  |


**Return Value:**





---
## LockFactory





* Full name: \NGSOFT\Lock\LockFactory


### LockFactory::__construct



```php
LockFactory::__construct( mixed rootpath = '', int|float seconds, string owner = '' ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `rootpath` | **mixed** |  |
| `seconds` | **int\|float** |  |
| `owner` | **string** |  |


**Return Value:**





---
### LockFactory::createFileLock

Create a Php File Lock

```php
LockFactory::createFileLock( string name, int seconds, string owner = '', string rootpath = '' ): \NGSOFT\Lock\FileLock
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | **string** |  |
| `seconds` | **int** |  |
| `owner` | **string** |  |
| `rootpath` | **string** |  |


**Return Value:**





---
### LockFactory::createFileSystemLock

Create a .lock file inside the dame directory as the provided file

```php
LockFactory::createFileSystemLock( string|\NGSOFT\Filesystem\File file, int seconds, string owner = '' ): \NGSOFT\Lock\FileSystemLock
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `file` | **string\|\NGSOFT\Filesystem\File** |  |
| `seconds` | **int** |  |
| `owner` | **string** |  |


**Return Value:**





---
### LockFactory::createSQLiteLock

Create a SQLite Lock

```php
LockFactory::createSQLiteLock( string name, int seconds, string owner = '', string dbname = 'sqlocks.db3', string table = 'locks' ): \NGSOFT\Lock\SQLiteLock
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | **string** |  |
| `seconds` | **int** |  |
| `owner` | **string** |  |
| `dbname` | **string** |  |
| `table` | **string** |  |


**Return Value:**





---
### LockFactory::createNoLock

Create a NoLock

```php
LockFactory::createNoLock( string name, int seconds, string owner = '' ): \NGSOFT\Lock\NoLock
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | **string** |  |
| `seconds` | **int** |  |
| `owner` | **string** |  |


**Return Value:**





---
### LockFactory::createCacheLock

Create a lock using a PSR-6 Cache

```php
LockFactory::createCacheLock( \Psr\Cache\CacheItemPoolInterface cache, string name, int seconds, string owner = '' ): \NGSOFT\Lock\CacheLock
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `cache` | **\Psr\Cache\CacheItemPoolInterface** |  |
| `name` | **string** |  |
| `seconds` | **int** |  |
| `owner` | **string** |  |


**Return Value:**





---
### LockFactory::createSimpleCacheLock

Create a lock using a PSR-16 Cache

```php
LockFactory::createSimpleCacheLock( \Psr\SimpleCache\CacheInterface cache, string name, int seconds, string owner = '' ): \NGSOFT\Lock\SimpleCacheLock
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `cache` | **\Psr\SimpleCache\CacheInterface** |  |
| `name` | **string** |  |
| `seconds` | **int** |  |
| `owner` | **string** |  |


**Return Value:**





---
## LockServiceProvider





* Full name: \NGSOFT\Lock\LockServiceProvider
* This class implements: \NGSOFT\Container\ServiceProvider


### LockServiceProvider::provides

Get the services provided by the provider.

```php
LockServiceProvider::provides(  ): string[]
```





**Return Value:**





---
### LockServiceProvider::register

Register the service into the container

```php
LockServiceProvider::register( \NGSOFT\Container\ContainerInterface container ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `container` | **\NGSOFT\Container\ContainerInterface** |  |


**Return Value:**





---
## LockTimeout





* Full name: \NGSOFT\Lock\LockTimeout
* Parent class: 


## Logger





* Full name: \NGSOFT\Facades\Logger
* Parent class: \NGSOFT\Facades\Facade


### Logger::log

Logs with an arbitrary level.

```php
Logger::log( mixed level, string|\Stringable message, array context = [] ): void
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `level` | **mixed** |  |
| `message` | **string\|\Stringable** |  |
| `context` | **array** |  |


**Return Value:**





---
### Logger::emergency

System is unusable.

```php
Logger::emergency( string|\Stringable message, array context = [] ): void
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `message` | **string\|\Stringable** |  |
| `context` | **array** |  |


**Return Value:**





---
### Logger::alert

Action must be taken immediately.

```php
Logger::alert( string|\Stringable message, array context = [] ): void
```

Example: Entire website down, database unavailable, etc. This should
trigger the SMS alerts and wake you up.

* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `message` | **string\|\Stringable** |  |
| `context` | **array** |  |


**Return Value:**





---
### Logger::critical

Critical conditions.

```php
Logger::critical( string|\Stringable message, array context = [] ): void
```

Example: Application component unavailable, unexpected exception.

* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `message` | **string\|\Stringable** |  |
| `context` | **array** |  |


**Return Value:**





---
### Logger::error

Runtime errors that do not require immediate action but should typically
be logged and monitored.

```php
Logger::error( string|\Stringable message, array context = [] ): void
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `message` | **string\|\Stringable** |  |
| `context` | **array** |  |


**Return Value:**





---
### Logger::warning

Exceptional occurrences that are not errors.

```php
Logger::warning( string|\Stringable message, array context = [] ): void
```

Example: Use of deprecated APIs, poor use of an API, undesirable things
that are not necessarily wrong.

* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `message` | **string\|\Stringable** |  |
| `context` | **array** |  |


**Return Value:**





---
### Logger::notice

Normal but significant events.

```php
Logger::notice( string|\Stringable message, array context = [] ): void
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `message` | **string\|\Stringable** |  |
| `context` | **array** |  |


**Return Value:**





---
### Logger::info

Interesting events.

```php
Logger::info( string|\Stringable message, array context = [] ): void
```

Example: User logs in, SQL logs.

* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `message` | **string\|\Stringable** |  |
| `context` | **array** |  |


**Return Value:**





---
### Logger::debug

Detailed debug information.

```php
Logger::debug( string|\Stringable message, array context = [] ): void
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `message` | **string\|\Stringable** |  |
| `context` | **array** |  |


**Return Value:**





---
## LoggerAwareResolver

Injects Logger



* Full name: \NGSOFT\Container\Resolvers\LoggerAwareResolver
* Parent class: \NGSOFT\Container\Resolvers\ContainerResolver


### LoggerAwareResolver::resolve

Resolves an entry from the container

```php
LoggerAwareResolver::resolve( mixed value ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **mixed** |  |


**Return Value:**





---
### LoggerAwareResolver::getDefaultPriority

Set the default priority

```php
LoggerAwareResolver::getDefaultPriority(  ): int
```





**Return Value:**





---
## Map

The Map object holds key-value pairs and remembers the original insertion order of the keys.

Any value (both objects and primitive values) may be used as either a key or a value.

* Full name: \NGSOFT\DataStructure\Map
* This class implements: \ArrayAccess, \NGSOFT\DataStructure\ReversibleIterator, \Stringable, \JsonSerializable

**See Also:**

* https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Map - JS Map

### Map::clear

The clear() method removes all elements from a Map object.

```php
Map::clear(  ): void
```





**Return Value:**





---
### Map::delete

The delete() method removes the specified element from a Map object by key.

```php
Map::delete( mixed key ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `key` | **mixed** |  |


**Return Value:**





---
### Map::get

The get() method returns a specified element from a Map object.

```php
Map::get( mixed key ): mixed
```

If the value that is associated to the provided key is an object,
then you will get a reference to that object and any change made
to that object will effectively modify it inside the Map object.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `key` | **mixed** |  |


**Return Value:**





---
### Map::search

The search() method returns the first key match from a value

```php
Map::search( mixed value ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **mixed** |  |


**Return Value:**





---
### Map::set

The set() method adds or updates an element with a specified key and a value to a Map object.

```php
Map::set( mixed key, mixed value ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `key` | **mixed** |  |
| `value` | **mixed** |  |


**Return Value:**





---
### Map::add

The add() method adds an element with a specified key and a value to a Map object if it does'n already exists.

```php
Map::add( mixed key, mixed value ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `key` | **mixed** |  |
| `value` | **mixed** |  |


**Return Value:**





---
### Map::has

The has() method returns a boolean indicating whether an element with the specified key exists or not.

```php
Map::has( mixed key ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `key` | **mixed** |  |


**Return Value:**





---
### Map::keys

The keys() method returns a new iterator object that contains the keys for each element in the Map object in insertion order

```php
Map::keys( \NGSOFT\DataStructure\Sort sort = Sort::ASC ): iterable
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sort` | **\NGSOFT\DataStructure\Sort** |  |


**Return Value:**





---
### Map::values

The values() method returns a new iterator object that contains the values for each element in the Map object in insertion order.

```php
Map::values( \NGSOFT\DataStructure\Sort sort = Sort::ASC ): iterable
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sort` | **\NGSOFT\DataStructure\Sort** |  |


**Return Value:**





---
### Map::entries

The entries() method returns a new iterator object that contains the [key, value] pairs for each element in the Map object in insertion order.

```php
Map::entries( \NGSOFT\DataStructure\Sort sort = Sort::ASC ): iterable
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sort` | **\NGSOFT\DataStructure\Sort** |  |


**Return Value:**





---
### Map::forEach

The forEach() method executes a provided function once per each key/value pair in the Map object, in insertion order.

```php
Map::forEach( callable callable ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `callable` | **callable** |  |


**Return Value:**





---
### Map::offsetExists

{@inheritdoc}

```php
Map::offsetExists( mixed offset ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |


**Return Value:**





---
### Map::offsetGet

{@inheritdoc}

```php
Map::offsetGet( mixed offset ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |


**Return Value:**





---
### Map::offsetSet

{@inheritdoc}

```php
Map::offsetSet( mixed offset, mixed value ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |
| `value` | **mixed** |  |


**Return Value:**





---
### Map::offsetUnset

{@inheritdoc}

```php
Map::offsetUnset( mixed offset ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |


**Return Value:**





---
### Map::count

{@inheritdoc}

```php
Map::count(  ): int
```





**Return Value:**





---
### Map::jsonSerialize

{@inheritdoc}

```php
Map::jsonSerialize(  ): mixed
```





**Return Value:**





---
### Map::__debugInfo

{@inheritdoc}

```php
Map::__debugInfo(  ): array
```





**Return Value:**





---
### Map::__serialize

{@inheritdoc}

```php
Map::__serialize(  ): array
```





**Return Value:**





---
### Map::__unserialize

{@inheritdoc}

```php
Map::__unserialize( array data ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `data` | **array** |  |


**Return Value:**





---
### Map::__clone

{@inheritdoc}

```php
Map::__clone(  ): void
```





**Return Value:**





---
## NoLock

NullLock



* Full name: \NGSOFT\Lock\NoLock
* Parent class: \NGSOFT\Lock\BaseLockStore


### NoLock::acquire

Acquires the lock.

```php
NoLock::acquire(  ): bool
```





**Return Value:**





---
### NoLock::forceRelease

{@inheritdoc}

```php
NoLock::forceRelease(  ): void
```





**Return Value:**





---
### NoLock::isAcquired

Returns whether or not the lock is acquired.

```php
NoLock::isAcquired(  ): bool
```





**Return Value:**





---
### NoLock::release

Release the lock.

```php
NoLock::release(  ): bool
```





**Return Value:**

False if lock not already acquired or not owned



---
## NotFound





* Full name: \NGSOFT\Container\Exceptions\NotFound
* Parent class: \NGSOFT\Container\Exceptions\ContainerError
* This class implements: \Psr\Container\NotFoundExceptionInterface


### NotFound::for



```php
NotFound::for( string id, \Throwable previous = null ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | **string** |  |
| `previous` | **\Throwable** |  |


**Return Value:**





---
## NullServiceProvider





* Full name: \NGSOFT\Container\NullServiceProvider
* This class implements: \NGSOFT\Container\ServiceProvider


### NullServiceProvider::__construct



```php
NullServiceProvider::__construct( string|array provides = [] ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `provides` | **string\|array** |  |


**Return Value:**





---
### NullServiceProvider::provides

Get the services provided by the provider.

```php
NullServiceProvider::provides(  ): string[]
```





**Return Value:**





---
### NullServiceProvider::register

Register the service into the container

```php
NullServiceProvider::register( \NGSOFT\Container\ContainerInterface container ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `container` | **\NGSOFT\Container\ContainerInterface** |  |


**Return Value:**





---
## OwnedList

Simulates one to many relationships found in databases



* Full name: \NGSOFT\DataStructure\OwnedList
* This class implements: \Stringable, \NGSOFT\DataStructure\ReversibleIterator, \JsonSerializable, \ArrayAccess


### OwnedList::create

Creates a new OwnedList for the given value

```php
OwnedList::create( int|float|string|object value ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **int\|float\|string\|object** |  |


**Return Value:**





---
### OwnedList::__construct



```php
OwnedList::__construct( int|float|string|object value ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **int\|float\|string\|object** |  |


**Return Value:**





---
### OwnedList::add

Adds a relationship between current value and the given value

```php
OwnedList::add( int|float|string|object value ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **int\|float\|string\|object** |  |


**Return Value:**





---
### OwnedList::delete

Removes a relationship between current value and the given value

```php
OwnedList::delete( int|float|string|object value ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **int\|float\|string\|object** |  |


**Return Value:**





---
### OwnedList::has

Checks if a relationship exists between current value and the given value

```php
OwnedList::has( int|float|string|object value ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **int\|float\|string\|object** |  |


**Return Value:**





---
### OwnedList::clear

Removes all relationships

```php
OwnedList::clear(  ): void
```





**Return Value:**





---
### OwnedList::entries

Iterates entries

```php
OwnedList::entries( \NGSOFT\DataStructure\Sort sort = Sort::ASC ): \Generator
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sort` | **\NGSOFT\DataStructure\Sort** |  |


**Return Value:**





---
### OwnedList::values

Iterates owned values

```php
OwnedList::values(  ): \Generator
```





**Return Value:**





---
### OwnedList::count

{@inheritdoc}

```php
OwnedList::count(  ): int
```





**Return Value:**





---
### OwnedList::offsetExists



```php
OwnedList::offsetExists( mixed offset ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |


**Return Value:**





---
### OwnedList::offsetGet



```php
OwnedList::offsetGet( mixed offset ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |


**Return Value:**





---
### OwnedList::offsetSet



```php
OwnedList::offsetSet( mixed offset, mixed value ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |
| `value` | **mixed** |  |


**Return Value:**





---
### OwnedList::offsetUnset



```php
OwnedList::offsetUnset( mixed offset ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |


**Return Value:**





---
### OwnedList::jsonSerialize

{@inheritdoc}

```php
OwnedList::jsonSerialize(  ): mixed
```





**Return Value:**





---
### OwnedList::__debugInfo

{@inheritdoc}

```php
OwnedList::__debugInfo(  ): array
```





**Return Value:**





---
### OwnedList::__serialize

{@inheritdoc}

```php
OwnedList::__serialize(  ): array
```





**Return Value:**





---
### OwnedList::__unserialize

{@inheritdoc}

```php
OwnedList::__unserialize( array data ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `data` | **array** |  |


**Return Value:**





---
### OwnedList::__clone

{@inheritdoc}

```php
OwnedList::__clone(  ): void
```





**Return Value:**





---
## ParameterResolver





* Full name: \NGSOFT\Container\ParameterResolver


### ParameterResolver::__construct



```php
ParameterResolver::__construct( \NGSOFT\Container\ContainerInterface container ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `container` | **\NGSOFT\Container\ContainerInterface** |  |


**Return Value:**





---
### ParameterResolver::canResolve



```php
ParameterResolver::canResolve( string id, mixed value ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | **string** |  |
| `value` | **mixed** |  |


**Return Value:**





---
### ParameterResolver::resolve



```php
ParameterResolver::resolve( string|array|object callable, array providedParameters = [] ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `callable` | **string\|array\|object** |  |
| `providedParameters` | **array** |  |


**Return Value:**





---
## PrioritySet

A Priority Set is a set that sorts entries by priority



* Full name: \NGSOFT\DataStructure\PrioritySet
* This class implements: \Countable, \JsonSerializable, \Stringable, \IteratorAggregate


### PrioritySet::create

Create a new Set

```php
PrioritySet::create(  ): static
```



* This method is **static**.

**Return Value:**





---
### PrioritySet::add

The add() method adds a new element with a specified value with a given priority.

```php
PrioritySet::add( mixed value, int|\NGSOFT\DataStructure\Priority priority = Priority::MEDIUM ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **mixed** |  |
| `priority` | **int\|\NGSOFT\DataStructure\Priority** | &gt; 0 the highest the number, the highest the priority |


**Return Value:**





---
### PrioritySet::getPriority



```php
PrioritySet::getPriority( mixed value ): int
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **mixed** |  |


**Return Value:**





---
### PrioritySet::clear

The clear() method removes all elements from a Set object.

```php
PrioritySet::clear(  ): void
```





**Return Value:**





---
### PrioritySet::delete

The delete() method removes a specified value from a Set object, if it is in the set.

```php
PrioritySet::delete( mixed value ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **mixed** |  |


**Return Value:**





---
### PrioritySet::entries

The entries() method returns a new Iterator object that contains an array of [value, value] for each element in the Set object, in insertion order.

```php
PrioritySet::entries( \NGSOFT\DataStructure\Sort sort = Sort::DESC ): iterable
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sort` | **\NGSOFT\DataStructure\Sort** |  |


**Return Value:**





---
### PrioritySet::forEach

The forEach() method executes a provided function once for each value in the Set object, in insertion order.

```php
PrioritySet::forEach( callable callable ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `callable` | **callable** | ($value,$value, Set) |


**Return Value:**





---
### PrioritySet::has

The has() method returns a boolean indicating whether an element with the specified value exists in a Set object or not.

```php
PrioritySet::has( mixed value ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **mixed** |  |


**Return Value:**





---
### PrioritySet::isEmpty

Checks if set is empty

```php
PrioritySet::isEmpty(  ): bool
```





**Return Value:**





---
### PrioritySet::values

The values() method returns a new Iterator object that contains the values for each element in the Set object in insertion order.

```php
PrioritySet::values( \NGSOFT\DataStructure\Sort sort = Sort::DESC ): \Generator
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sort` | **\NGSOFT\DataStructure\Sort** |  |


**Return Value:**





---
### PrioritySet::count

{@inheritdoc}

```php
PrioritySet::count(  ): int
```





**Return Value:**





---
### PrioritySet::getIterator

{@inheritdoc}

```php
PrioritySet::getIterator(  ): \Traversable
```





**Return Value:**





---
### PrioritySet::jsonSerialize

{@inheritdoc}

```php
PrioritySet::jsonSerialize(  ): mixed
```





**Return Value:**





---
### PrioritySet::__debugInfo

{@inheritdoc}

```php
PrioritySet::__debugInfo(  ): array
```





**Return Value:**





---
### PrioritySet::__serialize



```php
PrioritySet::__serialize(  ): array
```





**Return Value:**





---
### PrioritySet::__unserialize



```php
PrioritySet::__unserialize( array data ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `data` | **array** |  |


**Return Value:**





---
### PrioritySet::__clone

{@inheritdoc}

```php
PrioritySet::__clone(  ): void
```





**Return Value:**





---
## Range

Returns a sequence of numbers, starting from 0 by default, and increments by 1 (by default), and stops before a specified number.



* Full name: \NGSOFT\DataStructure\Range
* This class implements: \NGSOFT\DataStructure\ReversibleIterator, \Stringable


### Range::__construct



```php
Range::__construct( int start, ?int stop = null, int step = 1 ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `start` | **int** |  |
| `stop` | **?int** |  |
| `step` | **int** |  |


**Return Value:**





---
### Range::create

Creates a Range

```php
Range::create( int start, ?int stop = null, int step = 1 ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `start` | **int** |  |
| `stop` | **?int** |  |
| `step` | **int** |  |


**Return Value:**





---
### Range::of

Get a range for a Countable

```php
Range::of( \Countable|array countable ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `countable` | **\Countable\|array** |  |


**Return Value:**





---
### Range::__debugInfo



```php
Range::__debugInfo(  ): array
```





**Return Value:**





---
### Range::__toString



```php
Range::__toString(  ): string
```





**Return Value:**





---
### Range::isEmpty

Checks if empty range

```php
Range::isEmpty(  ): bool
```





**Return Value:**





---
### Range::count



```php
Range::count(  ): int
```





**Return Value:**





---
### Range::entries

Iterates entries in sort order

```php
Range::entries( \NGSOFT\DataStructure\Sort sort = Sort::ASC ): iterable
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sort` | **\NGSOFT\DataStructure\Sort** |  |


**Return Value:**





---
## RegExp





* Full name: \NGSOFT\RegExp
* This class implements: \Stringable, \JsonSerializable


### RegExp::create

Initialize RegExp

```php
RegExp::create( string pattern, string|string[] flags = '' ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pattern` | **string** | full pattern or pattern without delimitters and modifiers |
| `flags` | **string\|string[]** | modifiers |


**Return Value:**





---
### RegExp::__construct

Initialize RegExp

```php
RegExp::__construct( string pattern, string|string[] flags = '' ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pattern` | **string** | full pattern or pattern without delimitters and modifiers |
| `flags` | **string\|string[]** | modifiers |


**Return Value:**




**See Also:**

* https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/RegExp - Same as that except pattern is a string

---
### RegExp::getLastIndex

Get the last index

```php
RegExp::getLastIndex(  ): int
```





**Return Value:**





---
### RegExp::setLastIndex

Set the Last Index

```php
RegExp::setLastIndex( int index ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `index` | **int** |  |


**Return Value:**





---
### RegExp::test

The test() method executes a search for a match between a regular expression and a specified string.

```php
RegExp::test( string str ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `str` | **string** |  |


**Return Value:**





---
### RegExp::exec

The exec() method executes a search for a match in a specified string. Returns a result array, or null.

```php
RegExp::exec( string str ): array|null
```

Will only gives the first result. if the global flag is set, the lastIndex from the previous match will be stored,
so you can loop through results (while loop).


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `str` | **string** | The string against which to match the regular expression |


**Return Value:**

If the match fails, the exec() method returns null, and sets lastIndex to 0.



---
### RegExp::replace

The replace() method replaces some or all matches of a this pattern in a string by a replacement,
and returns the result of the replacement as a new string.

```php
RegExp::replace( string str, string|\Stringable|callable replacement ): string
```

If global modifier is used all occurences will be replaced, else only the first occurence will be replaced.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `str` | **string** |  |
| `replacement` | **string\|\Stringable\|callable** |  |


**Return Value:**





---
### RegExp::search

The search() method executes a search for a match between a regular expression and a string.

```php
RegExp::search( string str ): int
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `str` | **string** |  |


**Return Value:**

The index of the first match between the regular expression and the given string, or -1 if no match was found.



---
### RegExp::split

The split() method divides a String into an ordered list of substrings,

```php
RegExp::split( string str, int limit = -1 ): array
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `str` | **string** |  |
| `limit` | **int** |  |


**Return Value:**





---
### RegExp::matchAll

The matchAll() method returns an iterator of all results matching a string against a regular expression, including capturing groups.

```php
RegExp::matchAll( string str ): \Traversable
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `str` | **string** |  |


**Return Value:**





---
### RegExp::match

The match() method retrieves the result of matching a string against a regular expression.

```php
RegExp::match( string str ): array|null
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `str` | **string** |  |


**Return Value:**





---
### RegExp::__unserialize

{@inheritdoc}

```php
RegExp::__unserialize( array data ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `data` | **array** |  |


**Return Value:**





---
### RegExp::__serialize

{@inheritdoc}

```php
RegExp::__serialize(  ): array
```





**Return Value:**





---
### RegExp::jsonSerialize

{@inheritdoc}

```php
RegExp::jsonSerialize(  ): mixed
```





**Return Value:**





---
### RegExp::__toString

{@inheritdoc}

```php
RegExp::__toString(  ): string
```





**Return Value:**





---
### RegExp::__debugInfo



```php
RegExp::__debugInfo(  ): array
```





**Return Value:**





---
## RegExpException





* Full name: \NGSOFT\Exceptions\RegExpException
* Parent class: 


### RegExpException::__construct



```php
RegExpException::__construct( \NGSOFT\RegExp regExp, string message = "", int code, \Throwable previous = null ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `regExp` | **\NGSOFT\RegExp** |  |
| `message` | **string** |  |
| `code` | **int** |  |
| `previous` | **\Throwable** |  |


**Return Value:**





---
### RegExpException::getRegExp

Get the RegExp Object

```php
RegExpException::getRegExp(  ): \NGSOFT\RegExp
```





**Return Value:**





---
## ResolverException





* Full name: \NGSOFT\Container\Exceptions\ResolverException
* Parent class: \NGSOFT\Container\Exceptions\ContainerError


### ResolverException::notTwice



```php
ResolverException::notTwice( object resolver ): mixed
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `resolver` | **object** |  |


**Return Value:**





---
### ResolverException::invalidCallable



```php
ResolverException::invalidCallable( mixed callable ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `callable` | **mixed** |  |


**Return Value:**





---
## Set

The Set object lets you store unique values of any type, whether primitive values or object references.



* Full name: \NGSOFT\DataStructure\Set
* This class implements: \NGSOFT\DataStructure\ReversibleIterator, \JsonSerializable, \Stringable


### Set::create

Create a new Set

```php
Set::create(  ): static
```



* This method is **static**.

**Return Value:**





---
### Set::add

The add() method appends a new element with a specified value to the end of a Set object.

```php
Set::add( mixed value ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **mixed** |  |


**Return Value:**





---
### Set::clear

The clear() method removes all elements from a Set object.

```php
Set::clear(  ): void
```





**Return Value:**





---
### Set::delete

The delete() method removes a specified value from a Set object, if it is in the set.

```php
Set::delete( mixed value ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **mixed** |  |


**Return Value:**





---
### Set::entries

The entries() method returns a new Iterator object that contains an array of [value, value] for each element in the Set object, in insertion order.

```php
Set::entries( \NGSOFT\DataStructure\Sort sort = Sort::ASC ): iterable
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sort` | **\NGSOFT\DataStructure\Sort** |  |


**Return Value:**





---
### Set::has

The has() method returns a boolean indicating whether an element with the specified value exists in a Set object or not.

```php
Set::has( mixed value ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **mixed** |  |


**Return Value:**





---
### Set::isEmpty

Checks if set is empty

```php
Set::isEmpty(  ): bool
```





**Return Value:**





---
### Set::values

The values() method returns a new Iterator object that contains the values for each element in the Set object in insertion order.

```php
Set::values( \NGSOFT\DataStructure\Sort sort = Sort::ASC ): iterable
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sort` | **\NGSOFT\DataStructure\Sort** |  |


**Return Value:**





---
### Set::count

{@inheritdoc}

```php
Set::count(  ): int
```





**Return Value:**





---
### Set::getIterator

{@inheritdoc}

```php
Set::getIterator(  ): \Traversable
```





**Return Value:**





---
### Set::jsonSerialize

{@inheritdoc}

```php
Set::jsonSerialize(  ): mixed
```





**Return Value:**





---
### Set::__debugInfo

{@inheritdoc}

```php
Set::__debugInfo(  ): array
```





**Return Value:**





---
### Set::__serialize



```php
Set::__serialize(  ): array
```





**Return Value:**





---
### Set::__unserialize



```php
Set::__unserialize( array data ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `data` | **array** |  |


**Return Value:**





---
### Set::__clone

{@inheritdoc}

```php
Set::__clone(  ): void
```





**Return Value:**





---
## SharedList

Simulates Many-To-Many relations found in database



* Full name: \NGSOFT\DataStructure\SharedList
* This class implements: \Countable, \IteratorAggregate, \JsonSerializable, \Stringable

**See Also:**

* https://en.wikipedia.org/wiki/Many-to-many_(data_model) - 

### SharedList::create

Create a new SharedList

```php
SharedList::create(  ): static
```



* This method is **static**.

**Return Value:**





---
### SharedList::clear



```php
SharedList::clear(  ): void
```





**Return Value:**





---
### SharedList::hasValue

Checks if value exists in the set

```php
SharedList::hasValue( int|float|string|object value ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **int\|float\|string\|object** |  |


**Return Value:**





---
### SharedList::has

Checks if relationship exists between 2 values

```php
SharedList::has( int|float|string|object value, int|float|string|object sharedValue ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **int\|float\|string\|object** |  |
| `sharedValue` | **int\|float\|string\|object** |  |


**Return Value:**





---
### SharedList::add

Add a relationship between 2 values

```php
SharedList::add( int|float|string|object value, int|float|string|object sharedValue ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **int\|float\|string\|object** |  |
| `sharedValue` | **int\|float\|string\|object** |  |


**Return Value:**





---
### SharedList::deleteValue

Removes a value and all its relationships

```php
SharedList::deleteValue( int|float|string|object value ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **int\|float\|string\|object** |  |


**Return Value:**





---
### SharedList::delete

Removes a relationship between 2 values

```php
SharedList::delete( int|float|string|object value, int|float|string|object sharedValue ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **int\|float\|string\|object** |  |
| `sharedValue` | **int\|float\|string\|object** |  |


**Return Value:**





---
### SharedList::get

Get value shared list

```php
SharedList::get( int|float|string|object value ): \NGSOFT\DataStructure\Set
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **int\|float\|string\|object** |  |


**Return Value:**





---
### SharedList::entries

Iterates all values shared lists

```php
SharedList::entries(  ): iterable
```





**Return Value:**





---
### SharedList::count



```php
SharedList::count(  ): int
```





**Return Value:**





---
### SharedList::getIterator



```php
SharedList::getIterator(  ): \Traversable
```





**Return Value:**





---
### SharedList::jsonSerialize



```php
SharedList::jsonSerialize(  ): mixed
```





**Return Value:**





---
### SharedList::__serialize



```php
SharedList::__serialize(  ): array
```





**Return Value:**





---
### SharedList::__unserialize



```php
SharedList::__unserialize( array data ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `data` | **array** |  |


**Return Value:**





---
### SharedList::__debugInfo



```php
SharedList::__debugInfo(  ): array
```





**Return Value:**





---
## SimpleArray

A base Collection



* Full name: \NGSOFT\DataStructure\SimpleArray
* Parent class: \NGSOFT\DataStructure\Collection


### SimpleArray::unshift

Prepend one or more elements to the beginning of an array

```php
SimpleArray::unshift( mixed values ): int
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `values` | **mixed** |  |


**Return Value:**





---
### SimpleArray::push

Appends one or more elements at the end of an array

```php
SimpleArray::push( mixed values ): int
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `values` | **mixed** |  |


**Return Value:**





---
### SimpleArray::shift

Shift an element off the beginning of array

```php
SimpleArray::shift(  ): mixed
```





**Return Value:**

the removed element



---
### SimpleArray::pop

Pop the element off the end of array

```php
SimpleArray::pop(  ): mixed
```





**Return Value:**

the removed element



---
### SimpleArray::indexOf

Returns the value index

```php
SimpleArray::indexOf( mixed value ): int
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **mixed** |  |


**Return Value:**

the index or -1 if not found



---
## SimpleCacheLock

Use SimpleCache to manage your locks



* Full name: \NGSOFT\Lock\SimpleCacheLock
* Parent class: \NGSOFT\Lock\CacheLockAbstract


### SimpleCacheLock::__construct



```php
SimpleCacheLock::__construct( \Psr\SimpleCache\CacheInterface cache, string|\Stringable name, int|float seconds, string|\Stringable owner = '', bool autoRelease = true ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `cache` | **\Psr\SimpleCache\CacheInterface** |  |
| `name` | **string\|\Stringable** |  |
| `seconds` | **int\|float** |  |
| `owner` | **string\|\Stringable** |  |
| `autoRelease` | **bool** |  |


**Return Value:**





---
### SimpleCacheLock::forceRelease

{@inheritdoc}

```php
SimpleCacheLock::forceRelease(  ): void
```





**Return Value:**





---
## SimpleIterator

The SimpleIterator can iterate everything in any order



* Full name: \NGSOFT\DataStructure\SimpleIterator
* This class implements: \NGSOFT\DataStructure\ReversibleIterator


### SimpleIterator::__construct



```php
SimpleIterator::__construct( iterable iterator ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `iterator` | **iterable** |  |


**Return Value:**





---
### SimpleIterator::of

Create a new SimpleIterator

```php
SimpleIterator::of( iterable iterable ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `iterable` | **iterable** |  |


**Return Value:**





---
### SimpleIterator::ofStringable

Creates a new SimpleIterator that iterates each characters

```php
SimpleIterator::ofStringable( string|\Stringable value ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **string\|\Stringable** |  |


**Return Value:**





---
### SimpleIterator::ofList

Creates an iterator from a list

```php
SimpleIterator::ofList( iterable&amp;\ArrayAccess&amp;\Countable value ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **iterable&amp;\ArrayAccess&amp;\Countable** |  |


**Return Value:**





---
### SimpleIterator::count



```php
SimpleIterator::count(  ): int
```





**Return Value:**





---
### SimpleIterator::entries

Iterates entries in sort order

```php
SimpleIterator::entries( \NGSOFT\DataStructure\Sort sort = Sort::ASC ): iterable
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sort` | **\NGSOFT\DataStructure\Sort** |  |


**Return Value:**





---
## SimpleObject

A base Collection



* Full name: \NGSOFT\DataStructure\SimpleObject
* Parent class: \NGSOFT\DataStructure\Collection


### SimpleObject::search

Searches the array for a given value and returns the first corresponding key if successful

```php
SimpleObject::search( mixed value ): int|string|null
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **mixed** |  |


**Return Value:**





---
### SimpleObject::__get

{@inheritdoc}

```php
SimpleObject::__get( string name ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | **string** |  |


**Return Value:**





---
### SimpleObject::__set

{@inheritdoc}

```php
SimpleObject::__set( string name, mixed value ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | **string** |  |
| `value` | **mixed** |  |


**Return Value:**





---
### SimpleObject::__unset

{@inheritdoc}

```php
SimpleObject::__unset( string name ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | **string** |  |


**Return Value:**





---
### SimpleObject::__isset

{@inheritdoc}

```php
SimpleObject::__isset( string name ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | **string** |  |


**Return Value:**





---
## SimpleServiceProvider





* Full name: \NGSOFT\Container\SimpleServiceProvider
* This class implements: \NGSOFT\Container\ServiceProvider


### SimpleServiceProvider::__construct



```php
SimpleServiceProvider::__construct( string|array provides, mixed register ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `provides` | **string\|array** |  |
| `register` | **mixed** |  |


**Return Value:**





---
### SimpleServiceProvider::provides

Get the services provided by the provider.

```php
SimpleServiceProvider::provides(  ): string[]
```





**Return Value:**





---
### SimpleServiceProvider::register

Register the service into the container

```php
SimpleServiceProvider::register( \NGSOFT\Container\ContainerInterface container ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `container` | **\NGSOFT\Container\ContainerInterface** |  |


**Return Value:**





---
## Slice





* Full name: \NGSOFT\DataStructure\Slice
* This class implements: \Stringable


### Slice::create

Creates a Slice instance

```php
Slice::create( ?int start = null, ?int stop = null, ?int step = null ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `start` | **?int** |  |
| `stop` | **?int** |  |
| `step` | **?int** |  |


**Return Value:**





---
### Slice::of

Create a Slice instance using python slice notation

```php
Slice::of( string slice ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `slice` | **string** |  |


**Return Value:**




**See Also:**

* https://www.bestprog.net/en/2019/12/07/python-strings-access-by-indexes-slices-get-a-fragment-of-a-string-examples/ - eg ':' '::' '0:1:' '10:2:-1' '1:'

---
### Slice::isValid

Checks if valid slice syntax

```php
Slice::isValid( string slice ): bool
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `slice` | **string** |  |


**Return Value:**





---
### Slice::__construct



```php
Slice::__construct( ?int start = null, ?int stop = null, ?int step = null ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `start` | **?int** |  |
| `stop` | **?int** |  |
| `step` | **?int** |  |


**Return Value:**





---
### Slice::getStart



```php
Slice::getStart(  ): ?int
```





**Return Value:**





---
### Slice::getStop



```php
Slice::getStop(  ): ?int
```





**Return Value:**





---
### Slice::getStep



```php
Slice::getStep(  ): ?int
```





**Return Value:**





---
### Slice::getIteratorFor



```php
Slice::getIteratorFor( array&amp;\ArrayAccess&amp;\Countable value ): \Traversable&lt;int&gt;
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **array&amp;\ArrayAccess&amp;\Countable** |  |


**Return Value:**





---
### Slice::getOffsetList



```php
Slice::getOffsetList( array&amp;\ArrayAccess&amp;\Countable value ): int[]
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **array&amp;\ArrayAccess&amp;\Countable** |  |


**Return Value:**





---
### Slice::slice

Returns a slice of an array like object

```php
Slice::slice( array&amp;\ArrayAccess&amp;\Countable value ): array
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **array&amp;\ArrayAccess&amp;\Countable** |  |


**Return Value:**





---
### Slice::join

Returns a String of a slice

```php
Slice::join( mixed glue, mixed value ): string
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `glue` | **mixed** |  |
| `value` | **mixed** |  |


**Return Value:**





---
### Slice::__debugInfo



```php
Slice::__debugInfo(  ): array
```





**Return Value:**





---
### Slice::__toString



```php
Slice::__toString(  ): string
```





**Return Value:**





---
## SQLiteLock

A SQLite database to manage your locks



* Full name: \NGSOFT\Lock\SQLiteLock
* Parent class: \NGSOFT\Lock\BaseLockStore


### SQLiteLock::__construct



```php
SQLiteLock::__construct( string|\Stringable name, int|float seconds, string|\PDO database = '', string|\Stringable owner = '', bool autoRelease = true, string table = 'locks' ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | **string\|\Stringable** | Lock name |
| `seconds` | **int\|float** | lock duration |
| `database` | **string\|\PDO** | db3 filename or pdo instance |
| `owner` | **string\|\Stringable** | the owner of the lock |
| `autoRelease` | **bool** | release lock when object is destroyed |
| `table` | **string** | table name to tuse for the locks |


**Return Value:**





---
### SQLiteLock::forceRelease

{@inheritdoc}

```php
SQLiteLock::forceRelease(  ): void
```





**Return Value:**





---
## StackableContainer





* Full name: \NGSOFT\Container\StackableContainer
* This class implements: \Psr\Container\ContainerInterface, \Stringable


### StackableContainer::__construct



```php
StackableContainer::__construct( \Psr\Container\ContainerInterface|array containers ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `containers` | **\Psr\Container\ContainerInterface\|array** |  |


**Return Value:**





---
### StackableContainer::hasContainer

Check if container already stacked

```php
StackableContainer::hasContainer( \Psr\Container\ContainerInterface container ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `container` | **\Psr\Container\ContainerInterface** |  |


**Return Value:**





---
### StackableContainer::addContainer

Stacks a new Container on top

```php
StackableContainer::addContainer( \Psr\Container\ContainerInterface container ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `container` | **\Psr\Container\ContainerInterface** |  |


**Return Value:**





---
### StackableContainer::get

{@inheritdoc}

```php
StackableContainer::get( string id ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | **string** |  |


**Return Value:**





---
### StackableContainer::has

{@inheritdoc}

```php
StackableContainer::has( string id ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | **string** |  |


**Return Value:**





---
## State

Basic Enum Class Support (Polyfill)
Adds the ability to class constants to work as php 8.1 backed enums cases



* Full name: \NGSOFT\Timer\State
* Parent class: \NGSOFT\Enums\Enum


## StopWatch





* Full name: \NGSOFT\Timer\StopWatch


### StopWatch::startTask

Starts a callable and returns result time

```php
StopWatch::startTask( callable task, mixed arguments ): \NGSOFT\Timer\StopWatchResult
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **callable** |  |
| `arguments` | **mixed** |  |


**Return Value:**





---
### StopWatch::startTaskWithStartTime



```php
StopWatch::startTaskWithStartTime( mixed task, int|float startTime, bool highResolution = false ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |
| `startTime` | **int\|float** |  |
| `highResolution` | **bool** |  |


**Return Value:**





---
### StopWatch::__construct



```php
StopWatch::__construct( mixed|callable task = self::DEFAULT_TASK, bool highResolution = true ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed\|callable** | can be anything |
| `highResolution` | **bool** | if True use hrtime() if available, else use microtime() |


**Return Value:**





---
### StopWatch::getTask



```php
StopWatch::getTask(  ): mixed
```





**Return Value:**





---
### StopWatch::executeTask



```php
StopWatch::executeTask( array arguments = [] ): \NGSOFT\Timer\StopWatchResult
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `arguments` | **array** |  |


**Return Value:**





---
### StopWatch::start

Starts the clock

```php
StopWatch::start( int|float|null startTime = null ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `startTime` | **int\|float\|null** | Set the start time |


**Return Value:**





---
### StopWatch::resume

Resumes the clock (only if paused)

```php
StopWatch::resume(  ): bool
```





**Return Value:**





---
### StopWatch::reset

Resets the clock

```php
StopWatch::reset(  ): void
```





**Return Value:**





---
### StopWatch::pause

Pauses the clock

```php
StopWatch::pause( ?bool &success = null ): \NGSOFT\Timer\StopWatchResult
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `success` | **?bool** | True if operation succeeded |


**Return Value:**

Current time



---
### StopWatch::stop

Stops the clock

```php
StopWatch::stop( ?bool &success = null ): \NGSOFT\Timer\StopWatchResult
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `success` | **?bool** | True if operation succeeded |


**Return Value:**

Current time



---
### StopWatch::read

Reads the clock

```php
StopWatch::read(  ): \NGSOFT\Timer\StopWatchResult
```





**Return Value:**





---
### StopWatch::getLaps



```php
StopWatch::getLaps(  ): \Generator|\NGSOFT\Timer\StopWatchResult[]
```





**Return Value:**





---
### StopWatch::lap

Adds a lap time

```php
StopWatch::lap( ?string label = null ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `label` | **?string** |  |


**Return Value:**





---
### StopWatch::isStarted



```php
StopWatch::isStarted(  ): bool
```





**Return Value:**





---
### StopWatch::isPaused



```php
StopWatch::isPaused(  ): bool
```





**Return Value:**





---
### StopWatch::isStopped



```php
StopWatch::isStopped(  ): bool
```





**Return Value:**





---
## Text

Transform a scalar to its stringable representation



* Full name: \NGSOFT\DataStructure\Text
* This class implements: \Stringable, \Countable, \ArrayAccess, \JsonSerializable


### Text::create

Create new Text

```php
Text::create( mixed text ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `text` | **mixed** |  |


**Return Value:**





---
### Text::of

Create new Text

```php
Text::of( mixed text ): static
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `text` | **mixed** |  |


**Return Value:**





---
### Text::ofSegments

Create multiple segments of Text

```php
Text::ofSegments( mixed segments ): static[]
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `segments` | **mixed** |  |


**Return Value:**





---
### Text::__construct



```php
Text::__construct( mixed text = '' ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `text` | **mixed** |  |


**Return Value:**





---
### Text::copy

Get a Text Copy

```php
Text::copy(  ): static
```





**Return Value:**





---
### Text::indexOf

The indexOf() method, given one argument: a substring/regex to search for, searches the entire calling string, and returns the index of the first occurrence of the specified substring

```php
Text::indexOf( mixed needle, int offset ): int
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `needle` | **mixed** |  |
| `offset` | **int** |  |


**Return Value:**





---
### Text::search

Alias of indexOf

```php
Text::search( mixed needle ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `needle` | **mixed** |  |


**Return Value:**





---
### Text::lastIndexOf

The lastIndexOf() method, given one argument: a substring/regex to search for, searches the entire calling string, and returns the index of the last occurrence of the specified substring.

```php
Text::lastIndexOf( mixed needle, int offset ): int
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `needle` | **mixed** |  |
| `offset` | **int** |  |


**Return Value:**





---
### Text::at

The at() method takes an integer value and returns the character located at the specified offset

```php
Text::at( int offset ): string
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **int** |  |


**Return Value:**





---
### Text::concat

The concat() method concatenates the string arguments to the current Text

```php
Text::concat( mixed strings ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `strings` | **mixed** |  |


**Return Value:**





---
### Text::toLowerCase

Converts Text to lower case

```php
Text::toLowerCase(  ): static
```





**Return Value:**





---
### Text::toUpperCase

Converts Text to upper case

```php
Text::toUpperCase(  ): static
```





**Return Value:**





---
### Text::endsWith

The endsWith() method determines whether a string ends with the characters of a specified string, returning true or false as appropriate.

```php
Text::endsWith( mixed needle, bool ignoreCase = false ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `needle` | **mixed** |  |
| `ignoreCase` | **bool** |  |


**Return Value:**





---
### Text::startsWith

The startsWith() method determines whether a string begins with the characters of a specified string, returning true or false as appropriate.

```php
Text::startsWith( mixed needle, bool ignoreCase = false ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `needle` | **mixed** |  |
| `ignoreCase` | **bool** |  |


**Return Value:**





---
### Text::contains

The includes() method performs a search to determine whether one string may be found within another string/regex, returning true or false as appropriate.

```php
Text::contains( mixed needle, bool ignoreCase = false ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `needle` | **mixed** |  |
| `ignoreCase` | **bool** |  |


**Return Value:**





---
### Text::containsAll

Determine if a given string contains all needles

```php
Text::containsAll( iterable needles, bool ignoreCase = false ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `needles` | **iterable** |  |
| `ignoreCase` | **bool** |  |


**Return Value:**





---
### Text::includes

The includes() method performs a case-sensitive search to determine whether one string may be found within another string, returning true or false as appropriate.

```php
Text::includes( mixed needle ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `needle` | **mixed** |  |


**Return Value:**





---
### Text::match

The match() method retrieves the result of matching a string against a regular expression.

```php
Text::match( string pattern ): string[]
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pattern` | **string** |  |


**Return Value:**





---
### Text::matchAll

The matchAll() method returns an iterator of all results matching a string against a regular expression, including capturing groups.

```php
Text::matchAll( string pattern ): array
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pattern` | **string** |  |


**Return Value:**





---
### Text::padStart

Pad the left side of a string with another.

```php
Text::padStart( int length, mixed pad = ' ' ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `length` | **int** |  |
| `pad` | **mixed** |  |


**Return Value:**





---
### Text::padEnd

Pad the right side of a string with another.

```php
Text::padEnd( int length, mixed pad = ' ' ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `length` | **int** |  |
| `pad` | **mixed** |  |


**Return Value:**





---
### Text::pad

Pad on both sides of a string with another.

```php
Text::pad( int length, mixed pad = ' ' ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `length` | **int** |  |
| `pad` | **mixed** |  |


**Return Value:**





---
### Text::repeat

The repeat() method constructs and returns a new string which contains the specified number of copies of the string on which it was called,
concatenated together.

```php
Text::repeat( int times ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `times` | **int** |  |


**Return Value:**





---
### Text::replace

Replace the first occurrence of a given value in the string.

```php
Text::replace( mixed search, mixed replacement ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `search` | **mixed** |  |
| `replacement` | **mixed** |  |


**Return Value:**





---
### Text::replaceAll



```php
Text::replaceAll( mixed search, mixed replacement ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `search` | **mixed** |  |
| `replacement` | **mixed** |  |


**Return Value:**





---
### Text::substring

The substring() method returns the part of the string between the start and end indexes, or to the end of the string.

```php
Text::substring( int start, int|null end = null ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `start` | **int** |  |
| `end` | **int\|null** |  |


**Return Value:**





---
### Text::ltrim

Left Trim the string of the given characters.

```php
Text::ltrim( mixed chars ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `chars` | **mixed** |  |


**Return Value:**





---
### Text::trimStart

Alias of ltrim

```php
Text::trimStart( mixed chars ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `chars` | **mixed** |  |


**Return Value:**





---
### Text::rtrim

Right Trim the string of the given characters.

```php
Text::rtrim( mixed chars ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `chars` | **mixed** |  |


**Return Value:**





---
### Text::trimEnd

Alias of rtrim

```php
Text::trimEnd( mixed chars ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `chars` | **mixed** |  |


**Return Value:**





---
### Text::trim

Trim the string of the given characters.

```php
Text::trim( mixed chars ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `chars` | **mixed** |  |


**Return Value:**





---
### Text::capitalize

Return a copy of the string with its first character capitalized and the rest lowercased.

```php
Text::capitalize(  ): static
```





**Return Value:**





---
### Text::center

Return centered in a string of length width. Padding is done using the specified fillchar (default is an ASCII space).

```php
Text::center( int width, mixed fillchar = ' ' ): static
```

The original string is returned if width is less than or equal to len(s).


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `width` | **int** |  |
| `fillchar` | **mixed** |  |


**Return Value:**





---
### Text::expandtabs

Return a copy of the string where all tab characters are replaced by one or more spaces

```php
Text::expandtabs( int tabsize = 8 ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `tabsize` | **int** |  |


**Return Value:**





---
### Text::find

Return the lowest index in the string where substring sub is found within the slice s[start:end].

```php
Text::find( mixed sub, ?int start = null, ?int end = null ): int
```

Optional arguments start and end are interpreted as in slice notation. Return -1 if sub is not found.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sub` | **mixed** |  |
| `start` | **?int** |  |
| `end` | **?int** |  |


**Return Value:**





---
### Text::format

Perform a string formatting operation. The string on which this method is called can contain literal text or replacement fields delimited by braces {}.

```php
Text::format( mixed args ): static
```

Each replacement field contains either the numeric index of a positional argument, or the name of a keyword argument.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `args` | **mixed** |  |


**Return Value:**





---
### Text::index

Like find(), but raise ValueError when the substring is not found.

```php
Text::index( mixed sub, ?int start = null, ?int end = null ): int
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sub` | **mixed** |  |
| `start` | **?int** |  |
| `end` | **?int** |  |


**Return Value:**





---
### Text::isalnum

Return True if all characters in the string are alphanumeric and there is at least one character, False otherwise

```php
Text::isalnum(  ): bool
```





**Return Value:**





---
### Text::isalpha

Return True if all characters in the string are alphabetic and there is at least one character, False otherwise.

```php
Text::isalpha(  ): bool
```





**Return Value:**





---
### Text::isdecimal

Return True if all characters in the string are decimal characters and there is at least one character, False otherwise

```php
Text::isdecimal(  ): bool
```





**Return Value:**





---
### Text::isdigit

Return True if all characters in the string are digits and there is at least one character, False otherwise.

```php
Text::isdigit(  ): bool
```





**Return Value:**





---
### Text::islower

Return True if all cased characters in the string are lowercase and there is at least one cased character, False otherwise.

```php
Text::islower(  ): bool
```





**Return Value:**





---
### Text::isnumeric

Finds whether a variable is a number or a numeric string

```php
Text::isnumeric(  ): bool
```





**Return Value:**




**See Also:**

* https://www.php.net/manual/en/function.is-numeric.php - 

---
### Text::istitle

Return True if the string is a titlecased string and there is at least one character,
for example uppercase characters may only follow uncased characters and lowercase characters only cased ones.

```php
Text::istitle(  ): bool
```

Return False otherwise.



**Return Value:**





---
### Text::isspace

Return True if there are only whitespace characters in the string and there is at least one character, False otherwise.

```php
Text::isspace(  ): bool
```





**Return Value:**





---
### Text::isprintable

Return True if all characters in the string are printable or the string is empty, False otherwise.

```php
Text::isprintable(  ): bool
```





**Return Value:**





---
### Text::ispunct

Checks if all of the characters in the provided Text,  are punctuation character.

```php
Text::ispunct(  ): bool
```





**Return Value:**





---
### Text::iscontrol

Checks if all characters in Text are control characters

```php
Text::iscontrol(  ): bool
```





**Return Value:**





---
### Text::isupper

Return True if all characters in the string are uppercase and there is at least one lowercase character, False otherwise.

```php
Text::isupper(  ): bool
```





**Return Value:**





---
### Text::join

Return a string which is the concatenation of the strings in iterable.

```php
Text::join( mixed iterable ): static
```

The separator between elements is the Text providing this method.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `iterable` | **mixed** |  |


**Return Value:**





---
### Text::lower

Return a copy of the string with all characters converted to lowercase.

```php
Text::lower(  ): static
```





**Return Value:**





---
### Text::lstrip

Return a copy of the string with leading characters removed.

```php
Text::lstrip( mixed chars = null ): static
```

The chars argument is a string specifying the set of characters to be removed. If omitted or null, the chars argument defaults to removing whitespace.
The chars argument is not a prefix; rather, all combinations of its values are stripped:


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `chars` | **mixed** |  |


**Return Value:**





---
### Text::partition

Split the string at the first occurrence of sep,
and return a 3-tuple containing the part before the separator, the separator itself, and the part after the separator.

```php
Text::partition( mixed sep ): array
```

If the separator is not found, return a 3-tuple containing the string itself, followed by two empty strings.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sep` | **mixed** |  |


**Return Value:**





---
### Text::removeprefix

If the string starts with the prefix string,
return string[len(prefix):]. Otherwise, return a copy of the original string:

```php
Text::removeprefix( mixed prefix ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `prefix` | **mixed** |  |


**Return Value:**





---
### Text::removeSuffix

If the string ends with the suffix string and that suffix is not empty, return string[:-len(suffix)].

```php
Text::removeSuffix( mixed suffix ): static
```

Otherwise, return a copy of the original string:


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `suffix` | **mixed** |  |


**Return Value:**





---
### Text::reverse

Reverse the string

```php
Text::reverse(  ): static
```





**Return Value:**





---
### Text::rfind

Return the highest index in the string where substring sub is found, such that sub is contained within s[start:end].

```php
Text::rfind( mixed sub, ?int start = null, ?int end = null ): int
```

Optional arguments start and end are interpreted as in slice notation.
Return -1 on failure.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sub` | **mixed** |  |
| `start` | **?int** |  |
| `end` | **?int** |  |


**Return Value:**





---
### Text::rindex

Like rfind() but raises ValueError when the substring sub is not found.

```php
Text::rindex( mixed sub, ?int start = null, ?int end = null ): int
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sub` | **mixed** |  |
| `start` | **?int** |  |
| `end` | **?int** |  |


**Return Value:**





---
### Text::rpartition

Split the string at the last occurrence of sep,
and return a 3-tuple containing the part before the separator, the separator itself, and the part after the separator.

```php
Text::rpartition( mixed sep ): array
```

If the separator is not found, return a 3-tuple containing two empty strings, followed by the string itself.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sep` | **mixed** |  |


**Return Value:**





---
### Text::rstrip

Return a copy of the string with trailing characters removed.

```php
Text::rstrip( mixed chars = null ): static
```

The chars argument is a string specifying the set of characters to be removed.
If omitted or None, the chars argument defaults to removing whitespace.
The chars argument is not a suffix; rather, all combinations of its values are stripped:


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `chars` | **mixed** |  |


**Return Value:**





---
### Text::strip

Return a copy of the string with the leading and trailing characters removed.

```php
Text::strip( mixed chars = null ): static
```

The chars argument is a string specifying the set of characters to be removed.
If omitted or None, the chars argument defaults to removing whitespace.
The chars argument is not a prefix or suffix; rather, all combinations of its values are stripped:


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `chars` | **mixed** |  |


**Return Value:**





---
### Text::swapcase

Return a copy of the string with uppercase characters converted to lowercase and vice versa.

```php
Text::swapcase(  ): static
```





**Return Value:**





---
### Text::slice

The slice() method extracts a section of a string and returns it as a new string

```php
Text::slice( ?int start = null, ?int end = null, ?int step = null ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `start` | **?int** |  |
| `end` | **?int** |  |
| `step` | **?int** |  |


**Return Value:**





---
### Text::title

Return a titlecased version of the string where words start with an uppercase character and the remaining characters are lowercase.

```php
Text::title(  ): static
```





**Return Value:**





---
### Text::upper

Return a copy of the string with all the cased characters converted to uppercase

```php
Text::upper(  ): static
```





**Return Value:**





---
### Text::split

Return a list of the words in the string, using sep as the delimiter string.

```php
Text::split( mixed sep = null, int maxsplit = -1 ): static[]
```

If maxsplit is given, at most maxsplit splits are done (thus, the list will have at most maxsplit+1 elements).
If maxsplit is not specified or -1, then there is no limit on the number of splits (all possible splits are made).


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sep` | **mixed** |  |
| `maxsplit` | **int** |  |


**Return Value:**





---
### Text::rsplit

Return a list of the words in the string, using sep as the delimiter string.

```php
Text::rsplit( mixed sep = ' ', int maxsplit = -1 ): array
```

If maxsplit is given, at most maxsplit splits are done, the rightmost ones.
If sep is not specified or None, any whitespace string is a separator.
Except for splitting from the right, rsplit() behaves like split().


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `sep` | **mixed** |  |
| `maxsplit` | **int** |  |


**Return Value:**





---
### Text::splitlines

Return a list of the lines in the string, breaking at line boundaries.

```php
Text::splitlines( bool keepends = false ): array
```

Line breaks are not included in the resulting list unless keepends is given and true.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `keepends` | **bool** |  |


**Return Value:**





---
### Text::sprintf

Use sprintf to format string

```php
Text::sprintf( mixed args ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `args` | **mixed** |  |


**Return Value:**





---
### Text::ucfirst

Use ucfirst on the string

```php
Text::ucfirst(  ): static
```





**Return Value:**





---
### Text::lcfirst

Use lcfirst on the string

```php
Text::lcfirst(  ): static
```





**Return Value:**





---
### Text::append

Returns new Text with suffix added

```php
Text::append( mixed suffix ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `suffix` | **mixed** |  |


**Return Value:**





---
### Text::prepend

Returns new Text with prefix added

```php
Text::prepend( mixed prefix ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `prefix` | **mixed** |  |


**Return Value:**





---
### Text::isBase64

Checks if Text is base 64 encoded

```php
Text::isBase64(  ): bool
```





**Return Value:**





---
### Text::base64Encode

Returns a base64 decoded Text

```php
Text::base64Encode(  ): static
```





**Return Value:**





---
### Text::base64Decode

Returns a base64 decoded Text

```php
Text::base64Decode(  ): static
```





**Return Value:**





---
### Text::splitChars

Split the Text into multiple Text[]

```php
Text::splitChars( int length = 1 ): array
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `length` | **int** |  |


**Return Value:**





---
### Text::countChars

Count needle occurences inside Text
if using a regex as needle the search will be case sensitive

```php
Text::countChars( mixed needle, bool ignoreCase = false ): int
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `needle` | **mixed** |  |
| `ignoreCase` | **bool** |  |


**Return Value:**





---
### Text::isEquals

Checks if Text is the same as the provided needle

```php
Text::isEquals( mixed needle, bool ignoreCase = false ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `needle` | **mixed** |  |
| `ignoreCase` | **bool** |  |


**Return Value:**





---
### Text::ishexadecimal

Checks if string is hexadecimal number

```php
Text::ishexadecimal(  ): bool
```





**Return Value:**





---
### Text::length

Returns the length of the text

```php
Text::length(  ): int
```





**Return Value:**





---
### Text::size

Returns the byte size

```php
Text::size(  ): int
```





**Return Value:**





---
### Text::offsetExists



```php
Text::offsetExists( mixed offset ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |


**Return Value:**





---
### Text::offsetGet



```php
Text::offsetGet( mixed offset ): static
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |


**Return Value:**





---
### Text::offsetSet



```php
Text::offsetSet( mixed offset, mixed value ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |
| `value` | **mixed** |  |


**Return Value:**





---
### Text::offsetUnset



```php
Text::offsetUnset( mixed offset ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `offset` | **mixed** |  |


**Return Value:**





---
### Text::count



```php
Text::count(  ): int
```





**Return Value:**





---
### Text::isEmpty



```php
Text::isEmpty(  ): bool
```





**Return Value:**





---
### Text::jsonSerialize



```php
Text::jsonSerialize(  ): mixed
```





**Return Value:**





---
### Text::toString



```php
Text::toString(  ): string
```





**Return Value:**





---
### Text::__toString



```php
Text::__toString(  ): string
```





**Return Value:**





---
### Text::__serialize



```php
Text::__serialize(  ): array
```





**Return Value:**





---
### Text::__unserialize



```php
Text::__unserialize( array data ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `data` | **array** |  |


**Return Value:**





---
### Text::__debugInfo



```php
Text::__debugInfo(  ): array
```





**Return Value:**





---
## Timer





* Full name: \NGSOFT\Facades\Timer
* Parent class: \NGSOFT\Facades\Facade


### Timer::getWatch

Get a watch

```php
Timer::getWatch( mixed task = WatchFactory::DEFAULT_WATCH ): \NGSOFT\Timer\StopWatch
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |


**Return Value:**





---
### Timer::read

Reads the clock

```php
Timer::read( mixed task = WatchFactory::DEFAULT_WATCH ): \NGSOFT\Timer\StopWatchResult
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |


**Return Value:**





---
### Timer::start

Starts the clock

```php
Timer::start( mixed task = WatchFactory::DEFAULT_WATCH, int|float|null startTime = null ): bool
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |
| `startTime` | **int\|float\|null** |  |


**Return Value:**





---
### Timer::resume

Resumes the clock (only if paused)

```php
Timer::resume( mixed task = WatchFactory::DEFAULT_WATCH ): bool
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |


**Return Value:**





---
### Timer::reset

Resets the clock

```php
Timer::reset( mixed task = WatchFactory::DEFAULT_WATCH ): void
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |


**Return Value:**





---
### Timer::resetAll

Resets all the clocks

```php
Timer::resetAll(  ): void
```



* This method is **static**.

**Return Value:**





---
### Timer::pause

Pauses the clock

```php
Timer::pause( mixed task = WatchFactory::DEFAULT_WATCH, ?bool &success = null ): \NGSOFT\Timer\StopWatchResult
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |
| `success` | **?bool** |  |


**Return Value:**





---
### Timer::stop

Stops the clock

```php
Timer::stop( mixed task = WatchFactory::DEFAULT_WATCH, ?bool &success = null ): \NGSOFT\Timer\StopWatchResult
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |
| `success` | **?bool** |  |


**Return Value:**





---
### Timer::getLaps



```php
Timer::getLaps( mixed task = WatchFactory::DEFAULT_WATCH ): \Generator|\NGSOFT\Timer\StopWatchResult[]
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |


**Return Value:**





---
### Timer::lap

Adds a lap time

```php
Timer::lap( mixed task = WatchFactory::DEFAULT_WATCH, ?string label = null ): bool
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |
| `label` | **?string** |  |


**Return Value:**





---
## Tools

Useful Functions to use in my projects



* Full name: \NGSOFT\Tools


### Tools::safe_exec

Execute a callback and hides all php errors that can be thrown
Exceptions thrown inside the callback will be preserved

```php
Tools::safe_exec( callable callback, mixed args ): mixed
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `callback` | **callable** |  |
| `args` | **mixed** | args to be passed to the callback |


**Return Value:**





---
### Tools::errors_as_exceptions

Convenient Function used to convert php errors, warning, ... as Throwable

```php
Tools::errors_as_exceptions(  ): callable|null
```



* This method is **static**.

**Return Value:**





---
### Tools::suppress_errors

Set error handler to empty closure (as of php 8.1 @ doesn't works anymore)

```php
Tools::suppress_errors(  ): callable|null
```



* This method is **static**.

**Return Value:**





---
### Tools::normalize_path

Normalize pathnames

```php
Tools::normalize_path( string path ): string
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `path` | **string** |  |


**Return Value:**





---
### Tools::pushd

Change the current active directory
And stores the last position, use popd() to return to previous directory

```php
Tools::pushd( string dir ): bool
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `dir` | **string** |  |


**Return Value:**





---
### Tools::popd

Restore the last active directory changed by pushd

```php
Tools::popd(  ): string|false
```



* This method is **static**.

**Return Value:**

current directory



---
### Tools::each

Uses callback for each elements of the array and returns the value

```php
Tools::each( callable callback, iterable iterable ): iterable
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `callback` | **callable** |  |
| `iterable` | **iterable** |  |


**Return Value:**





---
### Tools::iterateAll

Iterate iterable

```php
Tools::iterateAll( iterable iterable ): array
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `iterable` | **iterable** |  |


**Return Value:**





---
### Tools::filter

Filters elements of an iterable using a callback function

```php
Tools::filter( callable callback, iterable iterable ): array
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `callback` | **callable** | accepts $value, $key, $array |
| `iterable` | **iterable** |  |


**Return Value:**





---
### Tools::search

Searches an iterable until element is found

```php
Tools::search( callable callback, iterable iterable ): null|mixed
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `callback` | **callable** |  |
| `iterable` | **iterable** |  |


**Return Value:**





---
### Tools::map

Same as the original except callback accepts more arguments and works with string keys

```php
Tools::map( callable callback, iterable iterable ): array
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `callback` | **callable** | accepts $value, $key, $array |
| `iterable` | **iterable** |  |


**Return Value:**





---
### Tools::some

Tests if at least one element in the iterable passes the test implemented by the provided function.

```php
Tools::some( callable callback, iterable iterable ): bool
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `callback` | **callable** |  |
| `iterable` | **iterable** |  |


**Return Value:**





---
### Tools::every

Tests if all elements in the iterable pass the test implemented by the provided function.

```php
Tools::every( callable callback, iterable iterable ): bool
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `callback` | **callable** |  |
| `iterable` | **iterable** |  |


**Return Value:**





---
### Tools::pull

Get a value(s) from the array, and remove it.

```php
Tools::pull( iterable|string|int keys, array|\ArrayAccess &iterable ): mixed
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `keys` | **iterable\|string\|int** |  |
| `iterable` | **array\|\ArrayAccess** |  |


**Return Value:**





---
### Tools::cloneArray

Clone all objects of an array recursively

```php
Tools::cloneArray( array array, bool recursive = true ): array
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `array` | **array** |  |
| `recursive` | **bool** |  |


**Return Value:**





---
### Tools::iterableToArray

Converts an iterable to an array recursively
if the keys are not string the will be indexed

```php
Tools::iterableToArray( iterable iterable ): array
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `iterable` | **iterable** |  |


**Return Value:**





---
### Tools::concat

Concatenate multiple values into the iterable provided recursively
If a provided value is iterable it will be merged into the iterable
(non numeric keys will be replaced if not iterable into the provided object)

```php
Tools::concat( array|\ArrayAccess &iterable, mixed values ): array|\ArrayAccess
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `iterable` | **array\|\ArrayAccess** |  |
| `values` | **mixed** |  |


**Return Value:**





---
### Tools::countValue

Count number of occurences of value

```php
Tools::countValue( mixed value, iterable iterable ): int
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **mixed** |  |
| `iterable` | **iterable** |  |


**Return Value:**





---
### Tools::isValidUrl

Checks if is a valid url

```php
Tools::isValidUrl( string url, bool webonly = false ): bool
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `url` | **string** |  |
| `webonly` | **bool** | Put local urls as invalid ( eg : &quot;http://localhost/index.php&quot; ) |


**Return Value:**




**See Also:**

* https://gist.github.com/dperini/729294 - 

---
### Tools::to_snake

Convert CamelCased to camel_cased

```php
Tools::to_snake( string camelCased ): string
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `camelCased` | **string** |  |


**Return Value:**





---
### Tools::toCamelCase

Convert snake_case to snakeCase

```php
Tools::toCamelCase( string snake_case ): string
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `snake_case` | **string** |  |


**Return Value:**





---
### Tools::millitime

Return current Unix timestamp in milliseconds

```php
Tools::millitime(  ): int
```



* This method is **static**.

**Return Value:**




**See Also:**

* https://stackoverflow.com/questions/3656713/how-to-get-current-time-in-milliseconds-in-php - 

---
### Tools::generate_uuid_v4

Generates a uuid V4

```php
Tools::generate_uuid_v4(  ): string
```



* This method is **static**.

**Return Value:**




**See Also:**

* https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid - 

---
### Tools::isAscii

Returns whether this string consists entirely of ASCII characters

```php
Tools::isAscii( string input ): bool
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `input` | **string** |  |


**Return Value:**





---
### Tools::isPrintableAscii

Returns whether this string consists entirely of printable ASCII characters

```php
Tools::isPrintableAscii( string input ): bool
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `input` | **string** |  |


**Return Value:**





---
### Tools::getFilesize

Get Human Readable file size

```php
Tools::getFilesize( int|float size, int precision = 2 ): string
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `size` | **int\|float** |  |
| `precision` | **int** |  |


**Return Value:**




**See Also:**

* https://gist.github.com/liunian/9338301 - 

---
### Tools::randomString

Generate a more truly "random" alpha-numeric string.

```php
Tools::randomString( int length = 16 ): string
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `length` | **int** |  |


**Return Value:**





---
### Tools::getWordSize

Get the size of the longest word on a string

```php
Tools::getWordSize( string|\Stringable string ): int
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `string` | **string\|\Stringable** |  |


**Return Value:**





---
### Tools::splitString

Split the string at the given length without cutting words

```php
Tools::splitString( string|\Stringable string, int &length = null ): array
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `string` | **string\|\Stringable** |  |
| `length` | **int** |  |


**Return Value:**





---
### Tools::join

Joins iterable together using provided glue

```php
Tools::join( mixed glue, iterable values ): string
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `glue` | **mixed** |  |
| `values` | **iterable** |  |


**Return Value:**





---
### Tools::format

Try to reproduce python format

```php
Tools::format( string message, mixed args ): string
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `message` | **string** |  |
| `args` | **mixed** |  |


**Return Value:**





---
### Tools::split

Split a stringable using provided separator

```php
Tools::split( mixed separator, mixed value, int limit = -1 ): array
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `separator` | **mixed** |  |
| `value` | **mixed** |  |
| `limit` | **int** |  |


**Return Value:**





---
### Tools::getExecutionTime

Get script execution time

```php
Tools::getExecutionTime( int precision = 6 ): float|int
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `precision` | **int** |  |


**Return Value:**





---
### Tools::pause

Pauses script execution for a given amount of time
combines sleep or usleep

```php
Tools::pause( int|float seconds ): void
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `seconds` | **int\|float** |  |


**Return Value:**





---
### Tools::msleep

Pauses script execution for a given amount of milliseconds

```php
Tools::msleep( int milliseconds ): void
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `milliseconds` | **int** |  |


**Return Value:**





---
### Tools::implements_class

Get class implementing given parent class from the loaded classes

```php
Tools::implements_class( string|object parentClass, bool instanciable = true ): array
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `parentClass` | **string\|object** |  |
| `instanciable` | **bool** |  |


**Return Value:**





---
### Tools::getClassConstants

Get Constants defined in a class

```php
Tools::getClassConstants( string|object class, bool public = true ): array
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `class` | **string\|object** |  |
| `public` | **bool** | if True returns only public visibility constants |


**Return Value:**





---
### Tools::callPrivateMethod

Call a method within an object ignoring its status

```php
Tools::callPrivateMethod( object instance, string method, mixed arguments ): mixed
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `instance` | **object** |  |
| `method` | **string** |  |
| `arguments` | **mixed** |  |


**Return Value:**





---
## TypeCheck

Checks for mixed union/intersection types



* Full name: \NGSOFT\Tools\TypeCheck


### TypeCheck::assertType

Check the given value against the supplied types and throw TypeError if not valid

```php
TypeCheck::assertType( string name, mixed value, string types ): void
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | **string** | name to be displayed on error |
| `value` | **mixed** | the value to check |
| `types` | **string** | the types |


**Return Value:**





---
### TypeCheck::assertTypeMethod

Check the given value against the supplied types and throw TypeError if not valid

```php
TypeCheck::assertTypeMethod( string|array method, mixed value, string types ): void
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `method` | **string\|array** |  |
| `value` | **mixed** |  |
| `types` | **string** |  |


**Return Value:**





---
### TypeCheck::checkType

Can check a mix of intersection and union

```php
TypeCheck::checkType( mixed value, string types ): bool
```

eg TypeCheck::checkType([], 'Traversable & ArrayAccess | array')
or TypeCheck::checkType([], 'Traversable&ArrayAccess|array')
or TypeCheck::checkType([], \Traversable::class, '&', \ArrayAccess::class, 'array')
or TypeCheck::checkType([], \Traversable::class, '&', \ArrayAccess::class, '|','array')
or TypeCheck::checkType([], \Traversable::class, TypeCheck::INTERSECTION, \ArrayAccess::class, TypeCheck::UNION,'array')
the use of TypeCheck::UNION is not required
eg: TypeCheck::checkType([], 'string', 'object', 'array') will check 'string|object|array'

* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `value` | **mixed** |  |
| `types` | **string** |  |


**Return Value:**





---
## Units

Basic Enum Class Support (Polyfill)
Adds the ability to class constants to work as php 8.1 backed enums cases



* Full name: \NGSOFT\Timer\Units
* Parent class: \NGSOFT\Enums\Enum


### Units::getStep



```php
Units::getStep(  ): int|float
```





**Return Value:**





---
### Units::getPlural



```php
Units::getPlural(  ): string
```





**Return Value:**





---
### Units::getSingular



```php
Units::getSingular(  ): string
```





**Return Value:**





---
## WatchFactory





* Full name: \NGSOFT\Timer\WatchFactory


### WatchFactory::__construct



```php
WatchFactory::__construct( bool highResolution = false ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `highResolution` | **bool** |  |


**Return Value:**





---
### WatchFactory::getWatch

Get a watch

```php
WatchFactory::getWatch( mixed task = self::DEFAULT_WATCH ): \NGSOFT\Timer\StopWatch
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |


**Return Value:**





---
### WatchFactory::read

Reads the clock

```php
WatchFactory::read( mixed task = self::DEFAULT_WATCH ): \NGSOFT\Timer\StopWatchResult
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |


**Return Value:**





---
### WatchFactory::start

Starts the clock

```php
WatchFactory::start( mixed task = self::DEFAULT_WATCH, int|float|null startTime = null ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |
| `startTime` | **int\|float\|null** |  |


**Return Value:**





---
### WatchFactory::resume

Resumes the clock (only if paused)

```php
WatchFactory::resume( mixed task = self::DEFAULT_WATCH ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |


**Return Value:**





---
### WatchFactory::reset

Resets the clock

```php
WatchFactory::reset( mixed task = self::DEFAULT_WATCH ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |


**Return Value:**





---
### WatchFactory::resetAll

Resets all the clocks

```php
WatchFactory::resetAll(  ): void
```





**Return Value:**





---
### WatchFactory::pause

Pauses the clock

```php
WatchFactory::pause( mixed task = self::DEFAULT_WATCH, bool &success = null ): \NGSOFT\Timer\StopWatchResult
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |
| `success` | **bool** |  |


**Return Value:**





---
### WatchFactory::stop

Stops the clock

```php
WatchFactory::stop( mixed task = self::DEFAULT_WATCH, bool &success = null ): \NGSOFT\Timer\StopWatchResult
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |
| `success` | **bool** |  |


**Return Value:**





---
### WatchFactory::getLaps



```php
WatchFactory::getLaps( mixed task = self::DEFAULT_WATCH ): \Generator|\NGSOFT\Timer\StopWatchResult[]
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |


**Return Value:**





---
### WatchFactory::lap

Adds a lap time

```php
WatchFactory::lap( mixed task = self::DEFAULT_WATCH, ?string label = null ): bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `task` | **mixed** |  |
| `label` | **?string** |  |


**Return Value:**





---
