import 'package:json_annotation/json_annotation.dart';

part 'animal.g.dart';

@JsonSerializable()
class Animal {
  final int id;
  final int categoryId;
  final String name;
  final String? scientificName;
  final String description;
  final String? image;
  final List<String>? gallery;
  final String? habitat;
  final String? conservationStatus;
  final List<String>? diet;
  final String? age;
  final String? weight;
  final String? size;
  final String? funFacts;
  final List<String>? feedingTimes;
  final bool isVisible;
  final bool isFeatured;
  final int displayOrder;
  final DateTime createdAt;
  final DateTime updatedAt;

  Animal({
    required this.id,
    required this.categoryId,
    required this.name,
    this.scientificName,
    required this.description,
    this.image,
    this.gallery,
    this.habitat,
    this.conservationStatus,
    this.diet,
    this.age,
    this.weight,
    this.size,
    this.funFacts,
    this.feedingTimes,
    this.isVisible = true,
    this.isFeatured = false,
    this.displayOrder = 0,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Animal.fromJson(Map<String, dynamic> json) => _$AnimalFromJson(json);
  Map<String, dynamic> toJson() => _$AnimalToJson(this);

  Map<String, dynamic> toMap() {
    return {
      'id': id,
      'category_id': categoryId,
      'name': name,
      'scientific_name': scientificName,
      'description': description,
      'image': image,
      'gallery': gallery?.join(','),
      'habitat': habitat,
      'conservation_status': conservationStatus,
      'diet': diet?.join(','),
      'age': age,
      'weight': weight,
      'size': size,
      'fun_facts': funFacts,
      'feeding_times': feedingTimes?.join(','),
      'is_visible': isVisible ? 1 : 0,
      'is_featured': isFeatured ? 1 : 0,
      'display_order': displayOrder,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }

  factory Animal.fromMap(Map<String, dynamic> map) {
    return Animal(
      id: map['id'] as int,
      categoryId: map['category_id'] as int,
      name: map['name'] as String,
      scientificName: map['scientific_name'] as String?,
      description: map['description'] as String,
      image: map['image'] as String?,
      gallery: map['gallery'] != null 
          ? (map['gallery'] as String).split(',')
          : null,
      habitat: map['habitat'] as String?,
      conservationStatus: map['conservation_status'] as String?,
      diet: map['diet'] != null 
          ? (map['diet'] as String).split(',')
          : null,
      age: map['age'] as String?,
      weight: map['weight'] as String?,
      size: map['size'] as String?,
      funFacts: map['fun_facts'] as String?,
      feedingTimes: map['feeding_times'] != null 
          ? (map['feeding_times'] as String).split(',')
          : null,
      isVisible: map['is_visible'] == 1,
      isFeatured: map['is_featured'] == 1,
      displayOrder: map['display_order'] as int,
      createdAt: DateTime.parse(map['created_at'] as String),
      updatedAt: DateTime.parse(map['updated_at'] as String),
    );
  }
}
