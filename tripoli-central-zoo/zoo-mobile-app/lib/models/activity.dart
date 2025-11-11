import 'package:json_annotation/json_annotation.dart';

part 'activity.g.dart';

@JsonSerializable()
class Activity {
  final int id;
  final String name;
  final String activityType;
  final String description;
  final String? image;
  final int? facilityId;
  final int? animalId;
  final DateTime startTime;
  final DateTime endTime;
  final Map<String, dynamic>? recurrence;
  final int? durationMinutes;
  final int? capacity;
  final bool requiresBooking;
  final double? price;
  final String? ageRestriction;
  final bool isActive;
  final int displayOrder;
  final DateTime createdAt;
  final DateTime updatedAt;

  Activity({
    required this.id,
    required this.name,
    required this.activityType,
    required this.description,
    this.image,
    this.facilityId,
    this.animalId,
    required this.startTime,
    required this.endTime,
    this.recurrence,
    this.durationMinutes,
    this.capacity,
    this.requiresBooking = false,
    this.price,
    this.ageRestriction,
    this.isActive = true,
    this.displayOrder = 0,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Activity.fromJson(Map<String, dynamic> json) => _$ActivityFromJson(json);
  Map<String, dynamic> toJson() => _$ActivityToJson(this);

  Map<String, dynamic> toMap() {
    return {
      'id': id,
      'name': name,
      'activity_type': activityType,
      'description': description,
      'image': image,
      'facility_id': facilityId,
      'animal_id': animalId,
      'start_time': startTime.toIso8601String(),
      'end_time': endTime.toIso8601String(),
      'duration_minutes': durationMinutes,
      'capacity': capacity,
      'requires_booking': requiresBooking ? 1 : 0,
      'price': price,
      'age_restriction': ageRestriction,
      'is_active': isActive ? 1 : 0,
      'display_order': displayOrder,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }

  factory Activity.fromMap(Map<String, dynamic> map) {
    return Activity(
      id: map['id'] as int,
      name: map['name'] as String,
      activityType: map['activity_type'] as String,
      description: map['description'] as String,
      image: map['image'] as String?,
      facilityId: map['facility_id'] as int?,
      animalId: map['animal_id'] as int?,
      startTime: DateTime.parse(map['start_time'] as String),
      endTime: DateTime.parse(map['end_time'] as String),
      durationMinutes: map['duration_minutes'] as int?,
      capacity: map['capacity'] as int?,
      requiresBooking: map['requires_booking'] == 1,
      price: map['price'] != null ? (map['price'] as num).toDouble() : null,
      ageRestriction: map['age_restriction'] as String?,
      isActive: map['is_active'] == 1,
      displayOrder: map['display_order'] as int,
      createdAt: DateTime.parse(map['created_at'] as String),
      updatedAt: DateTime.parse(map['updated_at'] as String),
    );
  }
}
