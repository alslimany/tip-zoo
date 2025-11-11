import 'package:flutter/foundation.dart';
import 'package:zoo_mobile_app/models/facility.dart';
import 'package:zoo_mobile_app/services/api_service.dart';
import 'package:zoo_mobile_app/services/database_service.dart';

class FacilityProvider with ChangeNotifier {
  final ApiService _apiService = ApiService();
  List<Facility> _facilities = [];
  Facility? _selectedFacility;
  bool _isLoading = false;
  String? _error;

  List<Facility> get facilities => _facilities;
  Facility? get selectedFacility => _selectedFacility;
  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<void> fetchFacilities({bool forceRefresh = false}) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final facilities = await _apiService.getFacilities();
      _facilities = facilities;
      await _saveFacilitiesToLocal(facilities);
    } catch (e) {
      _facilities = await _loadFacilitiesFromLocal();
      _error = 'Using offline data: ${e.toString()}';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> selectFacility(int id) async {
    try {
      _selectedFacility = await _apiService.getFacility(id);
      notifyListeners();
    } catch (e) {
      _selectedFacility = await _loadFacilityFromLocal(id);
      notifyListeners();
    }
  }

  Future<void> _saveFacilitiesToLocal(List<Facility> facilities) async {
    final db = await DatabaseService.instance.database;
    final batch = db.batch();
    
    for (var facility in facilities) {
      batch.insert(
        'facilities',
        facility.toMap(),
        conflictAlgorithm: ConflictAlgorithm.replace,
      );
    }
    
    await batch.commit(noResult: true);
  }

  Future<List<Facility>> _loadFacilitiesFromLocal() async {
    final db = await DatabaseService.instance.database;
    final List<Map<String, dynamic>> maps = await db.query('facilities');
    return List.generate(maps.length, (i) => Facility.fromMap(maps[i]));
  }

  Future<Facility?> _loadFacilityFromLocal(int id) async {
    final db = await DatabaseService.instance.database;
    final List<Map<String, dynamic>> maps = await db.query(
      'facilities',
      where: 'id = ?',
      whereArgs: [id],
    );
    
    if (maps.isNotEmpty) {
      return Facility.fromMap(maps.first);
    }
    return null;
  }

  List<Facility> searchFacilities(String query) {
    if (query.isEmpty) return _facilities;
    
    return _facilities.where((facility) {
      return facility.name.toLowerCase().contains(query.toLowerCase()) ||
          facility.description.toLowerCase().contains(query.toLowerCase());
    }).toList();
  }
}
