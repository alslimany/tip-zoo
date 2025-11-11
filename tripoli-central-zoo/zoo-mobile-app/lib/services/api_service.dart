import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:zoo_mobile_app/models/animal.dart';
import 'package:zoo_mobile_app/models/facility.dart';
import 'package:zoo_mobile_app/models/activity.dart';

class ApiService {
  static const String baseUrl = 'http://localhost:8000/api';
  
  // Animals
  Future<List<Animal>> getAnimals() async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/animals'));
      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body)['data'];
        return data.map((json) => Animal.fromJson(json)).toList();
      }
      throw Exception('Failed to load animals');
    } catch (e) {
      rethrow;
    }
  }

  Future<Animal> getAnimal(int id) async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/animals/$id'));
      if (response.statusCode == 200) {
        return Animal.fromJson(json.decode(response.body)['data']);
      }
      throw Exception('Failed to load animal');
    } catch (e) {
      rethrow;
    }
  }

  // Facilities
  Future<List<Facility>> getFacilities() async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/facilities'));
      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body)['data'];
        return data.map((json) => Facility.fromJson(json)).toList();
      }
      throw Exception('Failed to load facilities');
    } catch (e) {
      rethrow;
    }
  }

  Future<Facility> getFacility(int id) async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/facilities/$id'));
      if (response.statusCode == 200) {
        return Facility.fromJson(json.decode(response.body)['data']);
      }
      throw Exception('Failed to load facility');
    } catch (e) {
      rethrow;
    }
  }

  // Activities
  Future<List<Activity>> getActivities() async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/activities'));
      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body)['data'];
        return data.map((json) => Activity.fromJson(json)).toList();
      }
      throw Exception('Failed to load activities');
    } catch (e) {
      rethrow;
    }
  }

  Future<Activity> getActivity(int id) async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/activities/$id'));
      if (response.statusCode == 200) {
        return Activity.fromJson(json.decode(response.body)['data']);
      }
      throw Exception('Failed to load activity');
    } catch (e) {
      rethrow;
    }
  }

  // Sync endpoint
  Future<Map<String, dynamic>> syncData(String lastSync) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/sync'),
        headers: {'Content-Type': 'application/json'},
        body: json.encode({'last_sync': lastSync}),
      );
      if (response.statusCode == 200) {
        return json.decode(response.body);
      }
      throw Exception('Failed to sync data');
    } catch (e) {
      rethrow;
    }
  }

  // Search
  Future<Map<String, dynamic>> search(String query) async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/search?q=$query'),
      );
      if (response.statusCode == 200) {
        return json.decode(response.body);
      }
      throw Exception('Failed to search');
    } catch (e) {
      rethrow;
    }
  }
}
