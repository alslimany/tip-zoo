import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:zoo_mobile_app/providers/animal_provider.dart';

class AnimalsScreen extends StatelessWidget {
  const AnimalsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Animals'),
      ),
      body: Consumer<AnimalProvider>(
        builder: (context, animalProvider, child) {
          if (animalProvider.isLoading) {
            return const Center(child: CircularProgressIndicator());
          }

          if (animalProvider.error != null) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.error_outline, size: 64, color: Colors.grey),
                  const SizedBox(height: 16),
                  Text(animalProvider.error!),
                ],
              ),
            );
          }

          final animals = animalProvider.animals;

          if (animals.isEmpty) {
            return const Center(
              child: Text('No animals available'),
            );
          }

          return ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: animals.length,
            itemBuilder: (context, index) {
              final animal = animals[index];
              return Card(
                margin: const EdgeInsets.only(bottom: 16),
                child: ListTile(
                  leading: animal.image != null
                      ? CircleAvatar(
                          backgroundImage: NetworkImage(animal.image!),
                        )
                      : const CircleAvatar(
                          child: Icon(Icons.pets),
                        ),
                  title: Text(animal.name),
                  subtitle: Text(animal.scientificName ?? ''),
                  trailing: const Icon(Icons.arrow_forward_ios, size: 16),
                  onTap: () {
                    // Navigate to animal detail
                  },
                ),
              );
            },
          );
        },
      ),
    );
  }
}
