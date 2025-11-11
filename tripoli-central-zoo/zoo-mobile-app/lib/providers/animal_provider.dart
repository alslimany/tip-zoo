import 'package:flutter/foundation.dart';
import 'package:zoo_mobile_app/models/animal.dart';
import 'package:zoo_mobile_app/services/api_service.dart';
import 'package:zoo_mobile_app/services/database_service.dart';

class AnimalProvider with ChangeNotifier {
  final ApiService _apiService = ApiService();
  List<Animal> _animals = [];
  Animal? _selectedAnimal;
  bool _isLoading = false;
  String? _error;

  List<Animal> get animals => _animals;
  Animal? get selectedAnimal => _selectedAnimal;
  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<void> fetchAnimals({bool forceRefresh = false}) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      // Try to fetch from API
      final animals = await _apiService.getAnimals();
      _animals = animals;
      
      // Save to local database
      await _saveAnimalsToLocal(animals);
    } catch (e) {
      // If API fails, load from local database
      _animals = await _loadAnimalsFromLocal();
      _error = 'Using offline data: ${e.toString()}';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> selectAnimal(int id) async {
    try {
      _selectedAnimal = await _apiService.getAnimal(id);
      notifyListeners();
    } catch (e) {
      // Load from local database
      _selectedAnimal = await _loadAnimalFromLocal(id);
      notifyListeners();
    }
  }

  Future<void> _saveAnimalsToLocal(List<Animal> animals) async {
    final db = await DatabaseService.instance.database;
    final batch = db.batch();
    
    for (var animal in animals) {
      batch.insert(
        'animals',
        animal.toMap(),
        conflictAlgorithm: ConflictAlgorithm.replace,
      );
    }
    
    await batch.commit(noResult: true);
  }

  Future<List<Animal>> _loadAnimalsFromLocal() async {
    final db = await DatabaseService.instance.database;
    final List<Map<String, dynamic>> maps = await db.query('animals');
    return List.generate(maps.length, (i) => Animal.fromMap(maps[i]));
  }

  Future<Animal?> _loadAnimalFromLocal(int id) async {
    final db = await DatabaseService.instance.database;
    final List<Map<String, dynamic>> maps = await db.query(
      'animals',
      where: 'id = ?',
      whereArgs: [id],
    );
    
    if (maps.isNotEmpty) {
      return Animal.fromMap(maps.first);
    }
    return null;
  }

  List<Animal> searchAnimals(String query) {
    if (query.isEmpty) return _animals;
    
    return _animals.where((animal) {
      return animal.name.toLowerCase().contains(query.toLowerCase()) ||
          animal.scientificName?.toLowerCase().contains(query.toLowerCase()) == true ||
          animal.description.toLowerCase().contains(query.toLowerCase());
    }).toList();
  }
}
