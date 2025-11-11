import 'package:json_annotation/json_annotation.dart';

part 'facility.g.dart';

@JsonSerializable()
class Facility {
  final int id;
  final int facilityTypeId;
  final String name;
  final String description;
  final String? image;
  final List<String>? gallery;
  final Map<String, String>? openingHours;
  final String? contactPhone;
  final String? contactEmail;
  final List<String>? amenities;
  final bool isAccessible;
  final bool isOpen;
  final int? capacity;
  final int displayOrder;
  final DateTime createdAt;
  final DateTime updatedAt;

  Facility({
    required this.id,
    required this.facilityTypeId,
    required this.name,
    required this.description,
    this.image,
    this.gallery,
    this.openingHours,
    this.contactPhone,
    this.contactEmail,
    this.amenities,
    this.isAccessible = true,
    this.isOpen = true,
    this.capacity,
    this.displayOrder = 0,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Facility.fromJson(Map<String, dynamic> json) => _$FacilityFromJson(json);
  Map<String, dynamic> toJson() => _$FacilityToJson(this);

  Map<String, dynamic> toMap() {
    return {
      'id': id,
      'facility_type_id': facilityTypeId,
      'name': name,
      'description': description,
      'image': image,
      'gallery': gallery?.join(','),
      'contact_phone': contactPhone,
      'contact_email': contactEmail,
      'amenities': amenities?.join(','),
      'is_accessible': isAccessible ? 1 : 0,
      'is_open': isOpen ? 1 : 0,
      'capacity': capacity,
      'display_order': displayOrder,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }

  factory Facility.fromMap(Map<String, dynamic> map) {
    return Facility(
      id: map['id'] as int,
      facilityTypeId: map['facility_type_id'] as int,
      name: map['name'] as String,
      description: map['description'] as String,
      image: map['image'] as String?,
      gallery: map['gallery'] != null 
          ? (map['gallery'] as String).split(',')
          : null,
      contactPhone: map['contact_phone'] as String?,
      contactEmail: map['contact_email'] as String?,
      amenities: map['amenities'] != null 
          ? (map['amenities'] as String).split(',')
          : null,
      isAccessible: map['is_accessible'] == 1,
      isOpen: map['is_open'] == 1,
      capacity: map['capacity'] as int?,
      displayOrder: map['display_order'] as int,
      createdAt: DateTime.parse(map['created_at'] as String),
      updatedAt: DateTime.parse(map['updated_at'] as String),
    );
  }
}
