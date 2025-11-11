import 'package:sqflite/sqflite.dart';
import 'package:path/path.dart';

class DatabaseService {
  static final DatabaseService instance = DatabaseService._init();
  static Database? _database;

  DatabaseService._init();

  Future<Database> get database async {
    if (_database != null) return _database!;
    _database = await _initDB('tripoli_zoo.db');
    return _database!;
  }

  Future<Database> _initDB(String filePath) async {
    final dbPath = await getDatabasesPath();
    final path = join(dbPath, filePath);

    return await openDatabase(
      path,
      version: 1,
      onCreate: _createDB,
    );
  }

  Future _createDB(Database db, int version) async {
    const idType = 'INTEGER PRIMARY KEY AUTOINCREMENT';
    const textType = 'TEXT NOT NULL';
    const intType = 'INTEGER NOT NULL';
    const boolType = 'INTEGER NOT NULL';

    // Animal Categories Table
    await db.execute('''
      CREATE TABLE animal_categories (
        id $idType,
        name $textType,
        description TEXT,
        icon TEXT,
        display_order $intType,
        is_active $boolType,
        created_at $textType,
        updated_at $textType
      )
    ''');

    // Animals Table
    await db.execute('''
      CREATE TABLE animals (
        id $idType,
        category_id $intType,
        name $textType,
        scientific_name TEXT,
        description $textType,
        image TEXT,
        gallery TEXT,
        habitat TEXT,
        conservation_status TEXT,
        diet TEXT,
        age TEXT,
        weight TEXT,
        size TEXT,
        fun_facts TEXT,
        feeding_times TEXT,
        is_visible $boolType,
        is_featured $boolType,
        display_order $intType,
        created_at $textType,
        updated_at $textType
      )
    ''');

    // Facility Types Table
    await db.execute('''
      CREATE TABLE facility_types (
        id $idType,
        name $textType,
        description TEXT,
        icon TEXT,
        display_order $intType,
        is_active $boolType,
        created_at $textType,
        updated_at $textType
      )
    ''');

    // Facilities Table
    await db.execute('''
      CREATE TABLE facilities (
        id $idType,
        facility_type_id $intType,
        name $textType,
        description $textType,
        image TEXT,
        gallery TEXT,
        contact_phone TEXT,
        contact_email TEXT,
        amenities TEXT,
        is_accessible $boolType,
        is_open $boolType,
        capacity INTEGER,
        display_order $intType,
        created_at $textType,
        updated_at $textType
      )
    ''');

    // Activities Table
    await db.execute('''
      CREATE TABLE activities (
        id $idType,
        name $textType,
        activity_type $textType,
        description $textType,
        image TEXT,
        facility_id INTEGER,
        animal_id INTEGER,
        start_time $textType,
        end_time $textType,
        duration_minutes INTEGER,
        capacity INTEGER,
        requires_booking $boolType,
        price REAL,
        age_restriction TEXT,
        is_active $boolType,
        display_order $intType,
        created_at $textType,
        updated_at $textType
      )
    ''');

    // Map Locations Table
    await db.execute('''
      CREATE TABLE map_locations (
        id $idType,
        name $textType,
        location_type $textType,
        reference_id $intType,
        coordinate_x REAL NOT NULL,
        coordinate_y REAL NOT NULL,
        svg_path TEXT,
        map_level $intType,
        description TEXT,
        is_interactive $boolType,
        created_at $textType,
        updated_at $textType
      )
    ''');

    // Sync Metadata Table
    await db.execute('''
      CREATE TABLE sync_metadata (
        id $idType,
        table_name $textType,
        last_sync $textType,
        sync_status $textType
      )
    ''');
  }

  Future<void> close() async {
    final db = await instance.database;
    db.close();
  }
}
