import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:zoo_mobile_app/providers/animal_provider.dart';
import 'package:zoo_mobile_app/providers/facility_provider.dart';
import 'package:zoo_mobile_app/providers/activity_provider.dart';
import 'package:zoo_mobile_app/providers/sync_provider.dart';
import 'package:zoo_mobile_app/screens/animals/animals_screen.dart';
import 'package:zoo_mobile_app/screens/facilities/facilities_screen.dart';
import 'package:zoo_mobile_app/screens/activities/activities_screen.dart';
import 'package:zoo_mobile_app/screens/map/map_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _selectedIndex = 0;

  final List<Widget> _screens = [
    const HomeTabScreen(),
    const AnimalsScreen(),
    const MapScreen(),
    const FacilitiesScreen(),
    const ActivitiesScreen(),
  ];

  @override
  void initState() {
    super.initState();
    _initializeData();
  }

  Future<void> _initializeData() async {
    final animalProvider = context.read<AnimalProvider>();
    final facilityProvider = context.read<FacilityProvider>();
    final activityProvider = context.read<ActivityProvider>();
    final syncProvider = context.read<SyncProvider>();

    await syncProvider.checkConnectivity();
    
    await Future.wait([
      animalProvider.fetchAnimals(),
      facilityProvider.fetchFacilities(),
      activityProvider.fetchActivities(),
    ]);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: _screens[_selectedIndex],
      bottomNavigationBar: NavigationBar(
        selectedIndex: _selectedIndex,
        onDestinationSelected: (index) {
          setState(() {
            _selectedIndex = index;
          });
        },
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.home_outlined),
            selectedIcon: Icon(Icons.home),
            label: 'Home',
          ),
          NavigationDestination(
            icon: Icon(Icons.pets_outlined),
            selectedIcon: Icon(Icons.pets),
            label: 'Animals',
          ),
          NavigationDestination(
            icon: Icon(Icons.map_outlined),
            selectedIcon: Icon(Icons.map),
            label: 'Map',
          ),
          NavigationDestination(
            icon: Icon(Icons.location_city_outlined),
            selectedIcon: Icon(Icons.location_city),
            label: 'Facilities',
          ),
          NavigationDestination(
            icon: Icon(Icons.event_outlined),
            selectedIcon: Icon(Icons.event),
            label: 'Activities',
          ),
        ],
      ),
    );
  }
}

class HomeTabScreen extends StatelessWidget {
  const HomeTabScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Tripoli Central Zoo'),
        actions: [
          Consumer<SyncProvider>(
            builder: (context, syncProvider, child) {
              return IconButton(
                icon: Icon(
                  syncProvider.isOnline ? Icons.cloud_done : Icons.cloud_off,
                  color: syncProvider.isOnline ? Colors.green : Colors.grey,
                ),
                onPressed: () {
                  if (syncProvider.isOnline) {
                    syncProvider.syncAll();
                  }
                },
              );
            },
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Welcome Card
            Card(
              elevation: 4,
              child: Container(
                padding: const EdgeInsets.all(20),
                width: double.infinity,
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [
                      Theme.of(context).colorScheme.primary,
                      Theme.of(context).colorScheme.primaryContainer,
                    ],
                  ),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Welcome to',
                      style: Theme.of(context).textTheme.titleMedium?.copyWith(
                            color: Colors.white,
                          ),
                    ),
                    Text(
                      'Tripoli Central Zoo',
                      style: Theme.of(context).textTheme.headlineMedium?.copyWith(
                            color: Colors.white,
                            fontWeight: FontWeight.bold,
                          ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Discover amazing wildlife and enjoy your visit!',
                      style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                            color: Colors.white70,
                          ),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 24),
            
            // Quick Actions
            Text(
              'Explore',
              style: Theme.of(context).textTheme.titleLarge?.copyWith(
                    fontWeight: FontWeight.bold,
                  ),
            ),
            const SizedBox(height: 16),
            GridView.count(
              crossAxisCount: 2,
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              mainAxisSpacing: 16,
              crossAxisSpacing: 16,
              children: [
                _buildQuickActionCard(
                  context,
                  'Animals',
                  Icons.pets,
                  Colors.orange,
                  () {},
                ),
                _buildQuickActionCard(
                  context,
                  'Interactive Map',
                  Icons.map,
                  Colors.blue,
                  () {},
                ),
                _buildQuickActionCard(
                  context,
                  'Facilities',
                  Icons.location_city,
                  Colors.green,
                  () {},
                ),
                _buildQuickActionCard(
                  context,
                  'Activities',
                  Icons.event,
                  Colors.purple,
                  () {},
                ),
              ],
            ),
            const SizedBox(height: 24),
            
            // Today's Activities
            Text(
              "Today's Activities",
              style: Theme.of(context).textTheme.titleLarge?.copyWith(
                    fontWeight: FontWeight.bold,
                  ),
            ),
            const SizedBox(height: 16),
            Consumer<ActivityProvider>(
              builder: (context, activityProvider, child) {
                final todayActivities = activityProvider.getTodayActivities();
                
                if (todayActivities.isEmpty) {
                  return const Card(
                    child: Padding(
                      padding: EdgeInsets.all(16),
                      child: Text('No activities scheduled for today'),
                    ),
                  );
                }
                
                return ListView.builder(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemCount: todayActivities.length,
                  itemBuilder: (context, index) {
                    final activity = todayActivities[index];
                    return Card(
                      child: ListTile(
                        leading: const Icon(Icons.event),
                        title: Text(activity.name),
                        subtitle: Text(
                          '${activity.startTime.hour}:${activity.startTime.minute.toString().padLeft(2, '0')}',
                        ),
                        trailing: const Icon(Icons.arrow_forward_ios, size: 16),
                      ),
                    );
                  },
                );
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildQuickActionCard(
    BuildContext context,
    String title,
    IconData icon,
    Color color,
    VoidCallback onTap,
  ) {
    return Card(
      elevation: 2,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Container(
          padding: const EdgeInsets.all(16),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(icon, size: 48, color: color),
              const SizedBox(height: 12),
              Text(
                title,
                style: Theme.of(context).textTheme.titleMedium?.copyWith(
                      fontWeight: FontWeight.bold,
                    ),
                textAlign: TextAlign.center,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
