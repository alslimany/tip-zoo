import 'package:flutter/foundation.dart';
import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:sqflite/sqflite.dart';
import 'package:zoo_mobile_app/services/api_service.dart';
import 'package:zoo_mobile_app/services/database_service.dart';

class SyncProvider with ChangeNotifier {
  final ApiService _apiService = ApiService();
  bool _isSyncing = false;
  DateTime? _lastSyncTime;
  String? _syncError;
  bool _isOnline = true;

  bool get isSyncing => _isSyncing;
  DateTime? get lastSyncTime => _lastSyncTime;
  String? get syncError => _syncError;
  bool get isOnline => _isOnline;

  SyncProvider() {
    _initConnectivityMonitoring();
  }

  void _initConnectivityMonitoring() {
    Connectivity().onConnectivityChanged.listen((ConnectivityResult result) {
      _isOnline = result != ConnectivityResult.none;
      notifyListeners();
    });
  }

  Future<void> syncAll() async {
    if (_isSyncing) return;

    _isSyncing = true;
    _syncError = null;
    notifyListeners();

    try {
      final lastSync = await _getLastSyncTime();
      final syncData = await _apiService.syncData(lastSync);
      
      await _processSyncData(syncData);
      
      _lastSyncTime = DateTime.now();
      await _saveLastSyncTime(_lastSyncTime!);
    } catch (e) {
      _syncError = e.toString();
    } finally {
      _isSyncing = false;
      notifyListeners();
    }
  }

  Future<String> _getLastSyncTime() async {
    final db = await DatabaseService.instance.database;
    final result = await db.query(
      'sync_metadata',
      where: 'table_name = ?',
      whereArgs: ['all'],
    );
    
    if (result.isNotEmpty) {
      return result.first['last_sync'] as String;
    }
    return DateTime(2000).toIso8601String();
  }

  Future<void> _saveLastSyncTime(DateTime time) async {
    final db = await DatabaseService.instance.database;
    await db.insert(
      'sync_metadata',
      {
        'table_name': 'all',
        'last_sync': time.toIso8601String(),
        'sync_status': 'completed',
      },
      conflictAlgorithm: ConflictAlgorithm.replace,
    );
  }

  Future<void> _processSyncData(Map<String, dynamic> syncData) async {
    final db = await DatabaseService.instance.database;
    final batch = db.batch();

    // Process animals
    if (syncData['animals'] != null) {
      for (var animal in syncData['animals']) {
        batch.insert('animals', animal, conflictAlgorithm: ConflictAlgorithm.replace);
      }
    }

    // Process facilities
    if (syncData['facilities'] != null) {
      for (var facility in syncData['facilities']) {
        batch.insert('facilities', facility, conflictAlgorithm: ConflictAlgorithm.replace);
      }
    }

    // Process activities
    if (syncData['activities'] != null) {
      for (var activity in syncData['activities']) {
        batch.insert('activities', activity, conflictAlgorithm: ConflictAlgorithm.replace);
      }
    }

    await batch.commit(noResult: true);
  }

  Future<void> checkConnectivity() async {
    final connectivityResult = await Connectivity().checkConnectivity();
    _isOnline = connectivityResult != ConnectivityResult.none;
    notifyListeners();
  }
}
