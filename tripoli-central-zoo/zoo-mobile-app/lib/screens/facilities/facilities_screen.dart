import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:zoo_mobile_app/providers/facility_provider.dart';

class FacilitiesScreen extends StatelessWidget {
  const FacilitiesScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Facilities'),
      ),
      body: Consumer<FacilityProvider>(
        builder: (context, facilityProvider, child) {
          if (facilityProvider.isLoading) {
            return const Center(child: CircularProgressIndicator());
          }

          final facilities = facilityProvider.facilities;

          if (facilities.isEmpty) {
            return const Center(
              child: Text('No facilities available'),
            );
          }

          return ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: facilities.length,
            itemBuilder: (context, index) {
              final facility = facilities[index];
              return Card(
                margin: const EdgeInsets.only(bottom: 16),
                child: ListTile(
                  leading: facility.image != null
                      ? CircleAvatar(
                          backgroundImage: NetworkImage(facility.image!),
                        )
                      : const CircleAvatar(
                          child: Icon(Icons.location_city),
                        ),
                  title: Text(facility.name),
                  subtitle: Text(
                    facility.isOpen ? 'Open' : 'Closed',
                    style: TextStyle(
                      color: facility.isOpen ? Colors.green : Colors.red,
                    ),
                  ),
                  trailing: const Icon(Icons.arrow_forward_ios, size: 16),
                  onTap: () {
                    // Navigate to facility detail
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
