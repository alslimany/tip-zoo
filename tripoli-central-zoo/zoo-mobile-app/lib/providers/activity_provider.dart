import 'package:flutter/foundation.dart';
import 'package:sqflite/sqflite.dart';
import 'package:zoo_mobile_app/models/activity.dart';
import 'package:zoo_mobile_app/services/api_service.dart';
import 'package:zoo_mobile_app/services/database_service.dart';

class ActivityProvider with ChangeNotifier {
  final ApiService _apiService = ApiService();
  List<Activity> _activities = [];
  Activity? _selectedActivity;
  bool _isLoading = false;
  String? _error;

  List<Activity> get activities => _activities;
  Activity? get selectedActivity => _selectedActivity;
  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<void> fetchActivities({bool forceRefresh = false}) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final activities = await _apiService.getActivities();
      _activities = activities;
      await _saveActivitiesToLocal(activities);
    } catch (e) {
      _activities = await _loadActivitiesFromLocal();
      _error = 'Using offline data: ${e.toString()}';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> selectActivity(int id) async {
    try {
      _selectedActivity = await _apiService.getActivity(id);
      notifyListeners();
    } catch (e) {
      _selectedActivity = await _loadActivityFromLocal(id);
      notifyListeners();
    }
  }

  Future<void> _saveActivitiesToLocal(List<Activity> activities) async {
    final db = await DatabaseService.instance.database;
    final batch = db.batch();
    
    for (var activity in activities) {
      batch.insert(
        'activities',
        activity.toMap(),
        conflictAlgorithm: ConflictAlgorithm.replace,
      );
    }
    
    await batch.commit(noResult: true);
  }

  Future<List<Activity>> _loadActivitiesFromLocal() async {
    final db = await DatabaseService.instance.database;
    final List<Map<String, dynamic>> maps = await db.query('activities');
    return List.generate(maps.length, (i) => Activity.fromMap(maps[i]));
  }

  Future<Activity?> _loadActivityFromLocal(int id) async {
    final db = await DatabaseService.instance.database;
    final List<Map<String, dynamic>> maps = await db.query(
      'activities',
      where: 'id = ?',
      whereArgs: [id],
    );
    
    if (maps.isNotEmpty) {
      return Activity.fromMap(maps.first);
    }
    return null;
  }

  List<Activity> getTodayActivities() {
    final now = DateTime.now();
    return _activities.where((activity) {
      return activity.startTime.year == now.year &&
          activity.startTime.month == now.month &&
          activity.startTime.day == now.day;
    }).toList();
  }

  List<Activity> searchActivities(String query) {
    if (query.isEmpty) return _activities;
    
    return _activities.where((activity) {
      return activity.name.toLowerCase().contains(query.toLowerCase()) ||
          activity.description.toLowerCase().contains(query.toLowerCase());
    }).toList();
  }
}
