import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:zoo_mobile_app/providers/activity_provider.dart';

class ActivitiesScreen extends StatelessWidget {
  const ActivitiesScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Activities & Events'),
      ),
      body: Consumer<ActivityProvider>(
        builder: (context, activityProvider, child) {
          if (activityProvider.isLoading) {
            return const Center(child: CircularProgressIndicator());
          }

          final activities = activityProvider.activities;

          if (activities.isEmpty) {
            return const Center(
              child: Text('No activities available'),
            );
          }

          return ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: activities.length,
            itemBuilder: (context, index) {
              final activity = activities[index];
              return Card(
                margin: const EdgeInsets.only(bottom: 16),
                child: ListTile(
                  leading: const CircleAvatar(
                    child: Icon(Icons.event),
                  ),
                  title: Text(activity.name),
                  subtitle: Text(
                    '${activity.startTime.hour}:${activity.startTime.minute.toString().padLeft(2, '0')} - ${activity.activityType}',
                  ),
                  trailing: activity.requiresBooking
                      ? const Chip(label: Text('Booking Required'))
                      : null,
                  onTap: () {
                    // Navigate to activity detail
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
