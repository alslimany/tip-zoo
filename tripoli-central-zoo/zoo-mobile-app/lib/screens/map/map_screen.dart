import 'package:flutter/material.dart';

class MapScreen extends StatefulWidget {
  const MapScreen({super.key});

  @override
  State<MapScreen> createState() => _MapScreenState();
}

class _MapScreenState extends State<MapScreen> {
  double _scale = 1.0;
  Offset _offset = Offset.zero;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Zoo Map'),
        actions: [
          IconButton(
            icon: const Icon(Icons.zoom_in),
            onPressed: () {
              setState(() {
                _scale = (_scale + 0.2).clamp(0.5, 3.0);
              });
            },
          ),
          IconButton(
            icon: const Icon(Icons.zoom_out),
            onPressed: () {
              setState(() {
                _scale = (_scale - 0.2).clamp(0.5, 3.0);
              });
            },
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () {
              setState(() {
                _scale = 1.0;
                _offset = Offset.zero;
              });
            },
          ),
        ],
      ),
      body: GestureDetector(
        onScaleUpdate: (details) {
          setState(() {
            _scale = (_scale * details.scale).clamp(0.5, 3.0);
            _offset += details.focalPointDelta;
          });
        },
        child: Container(
          color: Colors.grey[200],
          child: Center(
            child: Transform.scale(
              scale: _scale,
              child: Transform.translate(
                offset: _offset,
                child: CustomPaint(
                  size: Size(
                    MediaQuery.of(context).size.width,
                    MediaQuery.of(context).size.height - 100,
                  ),
                  painter: ZooMapPainter(),
                ),
              ),
            ),
          ),
        ),
      ),
      floatingActionButton: Column(
        mainAxisAlignment: MainAxisAlignment.end,
        children: [
          FloatingActionButton(
            heroTag: 'search',
            mini: true,
            onPressed: () {
              // Show search dialog
            },
            child: const Icon(Icons.search),
          ),
          const SizedBox(height: 8),
          FloatingActionButton(
            heroTag: 'location',
            mini: true,
            onPressed: () {
              // Show user location
            },
            child: const Icon(Icons.my_location),
          ),
        ],
      ),
    );
  }
}

class ZooMapPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = Colors.green
      ..style = PaintingStyle.fill;

    final strokePaint = Paint()
      ..color = Colors.green[800]!
      ..style = PaintingStyle.stroke
      ..strokeWidth = 2;

    // Draw main path
    final path = Path();
    path.moveTo(size.width * 0.5, size.height * 0.1);
    path.lineTo(size.width * 0.8, size.height * 0.3);
    path.lineTo(size.width * 0.8, size.height * 0.7);
    path.lineTo(size.width * 0.5, size.height * 0.9);
    path.lineTo(size.width * 0.2, size.height * 0.7);
    path.lineTo(size.width * 0.2, size.height * 0.3);
    path.close();

    canvas.drawPath(path, paint);
    canvas.drawPath(path, strokePaint);

    // Draw location markers
    final markerPaint = Paint()
      ..color = Colors.red
      ..style = PaintingStyle.fill;

    final locations = [
      Offset(size.width * 0.5, size.height * 0.2),
      Offset(size.width * 0.3, size.height * 0.4),
      Offset(size.width * 0.7, size.height * 0.4),
      Offset(size.width * 0.3, size.height * 0.6),
      Offset(size.width * 0.7, size.height * 0.6),
      Offset(size.width * 0.5, size.height * 0.8),
    ];

    for (var location in locations) {
      canvas.drawCircle(location, 8, markerPaint);
      canvas.drawCircle(
        location,
        8,
        Paint()
          ..color = Colors.white
          ..style = PaintingStyle.stroke
          ..strokeWidth = 2,
      );
    }

    // Draw legend
    final textPainter = TextPainter(
      text: const TextSpan(
        text: 'Interactive Zoo Map\n(Tap markers for details)',
        style: TextStyle(
          color: Colors.black87,
          fontSize: 16,
          fontWeight: FontWeight.bold,
        ),
      ),
      textDirection: TextDirection.ltr,
    );
    textPainter.layout();
    textPainter.paint(canvas, Offset(20, size.height - 50));
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
